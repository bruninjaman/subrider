'use client';

import { useState, useEffect } from 'react';

export default function MedicoesModal({ isOpen, onClose, id, measurements, references }) {
    const [isMenuOpen, setIsMenuOpen] = useState(true);
    const [activeCategory, setActiveCategory] = useState('cabecote');
    const [viewType, setViewType] = useState('measurement'); // 'reference' or 'measurement'
    const [formData, setFormData] = useState({});
    const [medicoesData, setMedicoesData] = useState({});

    // Categories definition
    const categories = [
        { id: 'dados', label: 'Dados', icon: 'fas fa-file-alt', desc: 'Resumo de todas as medições e referências' },
        { id: 'cabecote', label: 'Cabeçote', icon: 'fas fa-wrench', desc: 'Cabeça do motor com válvulas' },
        { id: 'motor', label: 'Motor', icon: 'fas fa-cog', desc: 'Unidade principal de combustão' },
        { id: 'virabrequim', label: 'Virabrequim', icon: 'fas fa-sync-alt', desc: 'Eixo de conversão do movimento' },
        { id: 'embreagem', label: 'Embreagem', icon: 'fas fa-circle-notch', desc: 'Sistema de transmissão de potência' },
        { id: 'bomba', label: 'Bombas', icon: 'fas fa-tint', desc: 'Sistema de circulação de fluidos' }
    ];

    useEffect(() => {
        if (isOpen) {
            document.body.style.overflow = 'hidden';
            // Process references
            const initialRefs = {};
            if (references) {
                Object.keys(references).forEach(table => {
                    const row = references[table];
                    if (row && row.medicoes) {
                        try {
                            initialRefs[table] = typeof row.medicoes === 'string' ? JSON.parse(row.medicoes) : row.medicoes;
                        } catch (e) { console.error(`Error parsing refs for ${table}`, e); }
                    }
                });
            }
            setFormData(initialRefs);

            // Process actual measurements
            const initialMedicoes = {};
            if (measurements) {
                Object.keys(measurements).forEach(table => {
                    const row = measurements[table];
                    if (row && row.medicoes) {
                        try {
                            initialMedicoes[table] = typeof row.medicoes === 'string' ? JSON.parse(row.medicoes) : row.medicoes;
                        } catch (e) { console.error(`Error parsing medicao for ${table}`, e); }
                    }
                });
            }
            setMedicoesData(initialMedicoes);
        } else {
            document.body.style.overflow = 'unset';
        }
        return () => { document.body.style.overflow = 'unset'; };
    }, [isOpen, references, measurements]);

    if (!isOpen) return null;

    // Handlers
    const handleValueChange = (type, table, field, value, index = null) => {
        if (type === 'reference') {
            setFormData(prev => ({
                ...prev,
                [table]: { ...(prev[table] || {}), [field]: value }
            }));
        } else {
            setMedicoesData(prev => {
                const tableData = prev[table] || {};
                if (index !== null) {
                    const fieldData = tableData[field] || {};
                    return {
                        ...prev,
                        [table]: { ...tableData, [field]: { ...fieldData, [index]: value } }
                    };
                } else {
                    return { ...prev, [table]: { ...tableData, [field]: value } };
                }
            });
        }
    };

    const handleSave = async (table, isRef) => {
        const payload = {
            table: table,
            measurements: isRef ? formData[table] : medicoesData[table],
            is_reference: isRef ? 1 : 0
        };

        try {
            const res = await fetch(`/api/ordemservico/measurements/${id}`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload)
            });
            if (res.ok) {
                alert('Dados salvos com sucesso!');
                window.location.reload();
            } else {
                alert('Erro ao salvar dados.');
            }
        } catch (err) {
            console.error(err);
            alert('Falha na conexão com o servidor.');
        }
    };

    const calculateShim = (type, lado, cil) => {
        const table = 'cabecote';
        const folga = parseFloat(medicoesData[table]?.[`${type}_folga_${lado}`]?.[cil]) || 0;
        const pastilhaAtual = parseFloat(medicoesData[table]?.[`${type}_pastilha_${lado}`]?.[cil]) || 0;
        const refRow = formData[table] || {};
        const refMin = parseFloat(refRow[`val_${type}_limite_min`]) || 0;
        const refMax = parseFloat(refRow[`val_${type}_limite_max`]) || 0;
        if (!folga || !pastilhaAtual || !refMin || !refMax) return null;
        if (folga >= refMin && folga <= refMax) return null;
        const ideal = (folga - ((refMax + refMin) / 2)) + pastilhaAtual;
        return ideal.toFixed(2).replace('.', ',');
    };

    const activeCatData = categories.find(c => c.id === activeCategory);

    return (
        <div id="dados" className="modal-overlay" onClick={onClose}>
            <div className="modal-content" onClick={e => e.stopPropagation()}>

                <div className="dados-header">
                    <div className="dados-header__content">
                        <h1 className="dados-header__title"><i className="fas fa-cogs"></i> DADOS TÉCNICOS E ESPECIFICAÇÕES</h1>
                        <div className="dados-header__subtitle">ORDEM DE SERVIÇO: <span className="ordem-number">#{id}</span></div>
                    </div>
                    <div className="header-actions">
                        {!isMenuOpen && (
                            <button className="back-to-menu-btn" onClick={() => setIsMenuOpen(true)}>
                                <i className="fas fa-th-large"></i> MENU PRINCIPAL
                            </button>
                        )}
                        <button className="close-btn" onClick={onClose}>&times;</button>
                    </div>
                </div>

                <div className="dados-main-container">
                    {isMenuOpen ? (
                        <div className="choice-menu-grid">
                            {categories.map(cat => (
                                <button key={cat.id} id={cat.id === 'dados' ? 'menu-card-dados' : ''} className="menu-card" onClick={() => { setActiveCategory(cat.id); setIsMenuOpen(false); }}>
                                    <div className="menu-card-icon"><i className={cat.icon}></i></div>
                                    <div className="menu-card-info">
                                        <h3>{cat.label}</h3>
                                        <p>{cat.desc}</p>
                                    </div>
                                    <i className="fas fa-chevron-right menu-card-arrow"></i>
                                </button>
                            ))}
                        </div>
                    ) : (
                        <>
                            <div className="componentes-navigation">
                                <div className="nav-header">
                                    <h2 className="nav-title"><i className="fas fa-motorcycle"></i> COMPONENTES</h2>
                                    <p className="nav-subtitle">Escolha uma categoria</p>
                                </div>
                                <div className="componentes-tabs">
                                    {categories.map(cat => (
                                        <button key={cat.id} id={cat.id === 'dados' ? 'tab-btn-dados' : ''} className={`tab-btn ${activeCategory === cat.id ? 'active' : ''}`} onClick={() => setActiveCategory(cat.id)}>
                                            <i className={cat.icon}></i><span>{cat.label}</span>
                                            <i className="fas fa-chevron-right icon-right"></i>
                                        </button>
                                    ))}
                                </div>
                            </div>

                            <div className="componentes-content">
                                <div className="component-header">
                                    <h3 className="component-title"><i className={activeCatData.icon}></i> {activeCatData.label}</h3>
                                    <div className="component-description">{activeCatData.desc}</div>
                                </div>

                                {activeCategory !== 'dados' ? (
                                    <>
                                        <div className="data-type-selector">
                                            <div className="selector-buttons">
                                                <button className={`toggle-btn ${viewType === 'reference' ? 'active' : ''}`} onClick={() => setViewType('reference')}>
                                                    <i className="fas fa-book"></i><span>REFERÊNCIAS</span>
                                                </button>
                                                <button className={`toggle-btn ${viewType === 'measurement' ? 'active' : ''}`} onClick={() => setViewType('measurement')}>
                                                    <i className="fas fa-ruler-combined"></i><span>MEDIÇÕES REAIS</span>
                                                </button>
                                            </div>
                                        </div>

                                        <div className="toggle-content">
                                            {viewType === 'reference' ? (
                                                renderReferenceForm(activeCategory, formData[activeCategory] || {}, handleValueChange, () => handleSave(activeCategory, true))
                                            ) : (
                                                renderMeasurementForm(activeCategory, formData[activeCategory] || {}, medicoesData[activeCategory] || {}, handleValueChange, () => handleSave(activeCategory, false), calculateShim)
                                            )}
                                        </div>
                                    </>
                                ) : (
                                    <div className="toggle-content">
                                        <div className="report-preview-container">
                                            {/* Using a simplified version or just informing */}
                                            <p style={{ color: '#888', marginBottom: '20px' }}>Esta seção exibe o relatório consolidado de todas as seções abaixo.</p>
                                            {/* In a real app we'd import MeasurementsReport here if it was a standalone component without side effects */}
                                            <div className="preview-placeholder">
                                                <i className="fas fa-print" style={{ fontSize: '3rem', marginBottom: '15px', color: '#333' }}></i>
                                                <p>O relatório completo está disponível na página principal da OS.</p>
                                                <button className="primary-btn" onClick={() => window.open(`/relatorio?ordem=${id}`, '_blank')} style={{ width: 'auto', padding: '10px 30px' }}>ABRIR VERSÃO PARA IMPRESSÃO</button>
                                            </div>
                                        </div>
                                    </div>
                                )}
                            </div>
                        </>
                    )}
                </div>

                <div className="footer-modal">
                    <button className="button--sair" onClick={onClose}><i className="fas fa-times"></i> FECHAR</button>
                    {/* Unsaved changes check would go here */}
                </div>
            </div>

            <style jsx>{`
                .modal-overlay { position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.85); display: flex; align-items: center; justify-content: center; z-index: 1000; backdrop-filter: blur(10px); padding: 20px; }
                .modal-content { background: #1a1c22; width: 100%; max-width: 1350px; height: 90vh; border-radius: 12px; display: flex; flex-direction: column; overflow: hidden; border: 1px solid #333; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5); }
                .dados-header { background: linear-gradient(135deg, #232530 0%, #1a1c22 100%); padding: 25px; border-bottom: 2px solid #e44c5c; position: relative; display: flex; align-items: center; justify-content: center; }
                .dados-header__title { color: #fff; font-size: 1.6rem; margin: 0; letter-spacing: 2px; font-weight: 800; text-align: center; }
                .dados-header__title i { color: #e44c5c; margin-right: 15px; }
                .dados-header__subtitle { color: #888; text-align: center; margin-top: 8px; font-size: 1rem; font-weight: 600; }
                .ordem-number { color: #e44c5c; }

                .header-actions { position: absolute; right: 25px; top: 50%; transform: translateY(-50%); display: flex; align-items: center; gap: 20px; }
                .close-btn { background: none; border: none; color: #fff; font-size: 2.5rem; cursor: pointer; opacity: 0.5; transition: opacity 0.2s; line-height: 1; }
                .close-btn:hover { opacity: 1; }
                .back-to-menu-btn { background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); color: #fff; padding: 8px 18px; border-radius: 6px; cursor: pointer; font-weight: 700; font-size: 0.85rem; display: flex; align-items: center; gap: 10px; transition: all 0.2s; }
                .back-to-menu-btn:hover { background: #e44c5c; border-color: #e44c5c; }

                .choice-menu-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(350px, 1fr)); gap: 25px; padding: 50px; width: 100%; overflow-y: auto; height: 100%; align-content: start; }
                .choice-menu-grid:has(#menu-card-dados) { grid-template-columns: 1fr; }
                #menu-card-dados { background: linear-gradient(135deg, #f39c12 0%, #e67e22 100%); border: none; }
                #menu-card-dados .menu-card-icon { background: rgba(255,255,255,0.2); color: #fff; }
                #menu-card-dados .menu-card-info p { color: rgba(255,255,255,0.7); }
                #menu-card-dados .menu-card-arrow { color: #fff; }

                .menu-card { background: #232530; border-radius: 15px; border: 1px solid #333; padding: 30px; display: flex; align-items: center; gap: 25px; cursor: pointer; transition: all 0.3s; text-align: left; position: relative; }
                .menu-card:hover { transform: translateY(-5px); border-color: #e44c5c; box-shadow: 0 15px 35px rgba(0,0,0,0.4); }
                .menu-card-icon { width: 70px; height: 70px; background: rgba(228, 76, 92, 0.1); border-radius: 15px; display: flex; align-items: center; justify-content: center; color: #e44c5c; font-size: 2rem; transition: all 0.3s; }
                .menu-card:hover .menu-card-icon { background: #e44c5c; color: #fff; }
                .menu-card-info h3 { color: #fff; margin: 0; font-size: 1.4rem; font-weight: 800; }
                .menu-card-info p { color: #666; margin: 5px 0 0 0; font-size: 0.9rem; }
                .menu-card-arrow { color: #444; margin-left: auto; transition: all 0.3s; }
                .menu-card:hover .menu-card-arrow { color: #e44c5c; transform: translateX(5px); }

                .preview-placeholder { background: #15171e; border: 2px dashed #333; border-radius: 15px; padding: 50px; text-align: center; color: #555; }
                .tab-btn.active#tab-btn-dados { background: linear-gradient(135deg, #f39c12 0%, #e67e22 100%); }
                
                .componentes-navigation { background: #15171e; border-right: 1px solid #2a2a2a; overflow-y: auto; display: flex; flex-direction: column; }
                .nav-header { padding: 25px; background: rgba(255,255,255,0.02); border-bottom: 1px solid #2a2a2a; flex-shrink: 0; }
                .nav-title { font-size: 0.9rem; color: #fff; margin: 0; display: flex; align-items: center; gap: 10px; font-weight: 700; opacity: 0.8; }
                .nav-subtitle { font-size: 0.75rem; color: #555; margin-top: 5px; }
                
                .tab-btn { width: 100%; padding: 18px 25px; background: transparent; border: none; color: #777; text-align: left; display: flex; align-items: center; gap: 15px; cursor: pointer; transition: all 0.3s ease; border-bottom: 1px solid #232530; position: relative; }
                .tab-btn i:first-of-type { font-size: 1.2rem; width: 25px; text-align: center; }
                .tab-btn .icon-right { margin-left: auto; font-size: 0.7rem; opacity: 0; transform: translateX(-10px); transition: all 0.3s; }
                .tab-btn:hover { background: rgba(255,255,255,0.03); color: #fff; }
                .tab-btn:hover .icon-right { opacity: 0.3; transform: translateX(0); }
                .tab-btn.active { background: linear-gradient(90deg, #e44c5c 0%, #d63851 100%); color: #fff; font-weight: 700; box-shadow: 0 4px 15px rgba(228, 76, 92, 0.2); }
                .tab-btn.active .icon-right { opacity: 1; transform: translateX(0); }

                .componentes-content { background: #1a1c22; display: flex; flex-direction: column; overflow: hidden; min-height: 0; }
                .component-header { padding: 35px 40px; border-bottom: 1px solid #2a2a2a; background: rgba(255,255,255,0.01); flex-shrink: 0; }
                .component-title { color: #fff; font-size: 1.8rem; margin: 0; font-weight: 700; display: flex; align-items: center; gap: 15px; }
                .component-title i { color: #e44c5c; }
                .component-description { color: #666; margin-top: 10px; font-size: 1rem; }

                .data-type-selector { padding: 25px 40px; background: #15171e; border-bottom: 1px solid #2a2a2a; flex-shrink: 0; }
                .selector-buttons { display: flex; background: #232530; padding: 5px; border-radius: 10px; width: fit-content; }
                .toggle-btn { background: transparent; border: none; color: #666; padding: 12px 30px; border-radius: 8px; cursor: pointer; display: flex; align-items: center; gap: 10px; font-weight: 700; transition: all 0.3s; font-size: 0.9rem; }
                .toggle-btn.active { background: #e44c5c; color: #fff; box-shadow: 0 4px 20px rgba(0,0,0,0.3); }

                .toggle-content { padding: 40px; flex: 1; overflow-y: auto; }
                .footer-modal { padding: 20px 40px; background: #15171e; border-top: 1px solid #2a2a2a; display: flex; justify-content: flex-end; flex-shrink: 0; }
                .button--sair { background: #333; color: #fff; border: none; padding: 12px 35px; border-radius: 8px; font-weight: 800; cursor: pointer; display: flex; align-items: center; gap: 12px; transition: all 0.3s; letter-spacing: 1px; }
                .button--sair:hover { background: #e44c5c; transform: translateY(-2px); box-shadow: 0 5px 15px rgba(228, 76, 92, 0.3); }

                @media (max-width: 1000px) { .dados-main-container { grid-template-columns: 1fr; } .componentes-navigation { display: none; } }
            `}</style>

            <style jsx global>{`
                .data-card { background: #232530; border-radius: 15px; padding: 30px; border: 1px solid #2a2a2a; margin-bottom: 30px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
                .data-card-title { color: #fff; font-size: 1.1rem; font-weight: 800; margin-bottom: 25px; display: flex; align-items: center; gap: 12px; border-bottom: 2px solid #e44c5c; padding-bottom: 15px; text-transform: uppercase; letter-spacing: 1.5px; }
                .form-grid { display: grid; gap: 25px; }
                .dados-main-container { display: grid; grid-template-columns: 280px 1fr; flex: 1; overflow: hidden; min-height: 0; }
                .dados-main-container:has(.choice-menu-grid) { grid-template-columns: 1fr; }
                .form-grid-3 { grid-template-columns: repeat(3, 1fr); }
                .form-grid-2 { grid-template-columns: repeat(2, 1fr); }
                .field-group label { display: block; color: #666; font-size: 0.85rem; margin-bottom: 10px; font-weight: 700; letter-spacing: 0.5px; }
                .field-input { width: 100%; background: #1a1c22; border: 2px solid #2a2a2a; padding: 14px; border-radius: 10px; color: #fff; transition: all 0.3s; font-size: 1rem; font-weight: 600; }
                .field-input:focus { border-color: #e44c5c; outline: none; background: #000; box-shadow: 0 0 0 3px rgba(228, 76, 92, 0.1); }
                .radio-group { display: flex; gap: 25px; }
                .radio-item { display: flex; align-items: center; gap: 10px; color: #fff; cursor: pointer; font-weight: 600; font-size: 0.95rem; }
                .radio-item input[type="radio"], .radio-item input[type="checkbox"] { accent-color: #e44c5c; width: 18px; height: 18px; }
                .primary-btn { background: linear-gradient(135deg, #e44c5c 0%, #c73650 100%); color: #fff; border: none; padding: 18px; border-radius: 10px; font-weight: 800; cursor: pointer; width: 100%; font-size: 1.1rem; transition: all 0.3s; letter-spacing: 1px; box-shadow: 0 4px 15px rgba(228, 76, 92, 0.3); }
                .primary-btn:hover { transform: translateY(-2px); box-shadow: 0 8px 25px rgba(228, 76, 92, 0.4); }

                table.medicoes-table { width: 100%; border-collapse: separate; border-spacing: 0; background: #1a1c22; border-radius: 12px; overflow: hidden; border: 1px solid #2a2a2a; }
                table.medicoes-table th, table.medicoes-table td { padding: 15px; border: 1px solid #2a2a2a; text-align: center; }
                table.medicoes-table thead th { background: #232530; color: #e44c5c; font-size: 0.8rem; font-weight: 800; letter-spacing: 1px; text-transform: uppercase; }
                table.medicoes-table .valvula-admissao { background: rgba(33, 150, 243, 0.03); }
                table.medicoes-table .valvula-escape { background: rgba(233, 30, 99, 0.03); }
                table.medicoes-table .group-heading { background: #232530; color: #fff; font-weight: 800; letter-spacing: 1px; font-size: 0.85rem; }
                table.medicoes-table .front-heading { background: rgba(228, 76, 92, 0.8); color: #fff; font-weight: 800; }
                table.medicoes-table .back-heading { background: rgba(25, 118, 210, 0.8); color: #fff; font-weight: 800; }
                .meas-field { width: 75px; background: #000; border: 2px solid #333; color: #00c063; padding: 10px; text-align: center; border-radius: 8px; font-weight: 800; font-size: 1.1rem; transition: all 0.3s; }
                .meas-field:focus { border-color: #e44c5c; outline: none; box-shadow: 0 0 10px rgba(0, 192, 99, 0.2); }
                .shim-display { font-size: 0.8rem; color: #ff9800; margin-top: 5px; font-weight: 800; background: rgba(255, 152, 0, 0.1); padding: 2px 5px; border-radius: 4px; }

                .ref-badge {
                    font-size: 0.6rem;
                    padding: 1px 6px;
                    border-radius: 4px;
                    font-weight: 900;
                    text-transform: uppercase;
                    letter-spacing: 0.5px;
                }
                .ref-badge--max { background: rgba(228, 76, 92, 0.15); color: #e44c5c; border: 1px solid rgba(228, 76, 92, 0.2); }
                .ref-badge--min { background: rgba(255, 193, 7, 0.15); color: #ffc107; border: 1px solid rgba(255, 193, 7, 0.2); }
                .ref-badge--ref { background: rgba(33, 150, 243, 0.15); color: #2196f3; border: 1px solid rgba(33, 150, 243, 0.2); }
                .ref-badge--range { background: rgba(255, 255, 255, 0.05); color: #888; border: 1px solid #333; }
            `}</style>
        </div>
    );
}

// --------------------------- HELPERS ---------------------------

function renderRefDisplay(value, type) {
    if (!value && value !== 0) return <span style={{ opacity: 0.3 }}>-</span>;

    if (type === 'RANGE') {
        const parts = String(value).split('-');
        const min = parts[0];
        const max = parts[1];
        if (!max) return renderRefDisplay(min, 'REF');
        return (
            <div style={{ display: 'flex', flexDirection: 'column', alignItems: 'center', justifyContent: 'center', gap: '4px' }}>
                <span style={{ fontWeight: '800', fontSize: '1rem', color: '#fff', whiteSpace: 'nowrap' }}>{min} ~ {max}</span>
                <span className="ref-badge ref-badge--range">FAIXA</span>
            </div>
        );
    }

    let label = type;
    let className = 'ref-badge';

    if (type === 'MAX') {
        label = 'MÁX';
        className += ' ref-badge--max';
    } else if (type === 'MIN') {
        label = 'MÍN';
        className += ' ref-badge--min';
    } else if (type === 'REF') {
        label = 'REF';
        className += ' ref-badge--ref';
    }

    return (
        <div style={{ display: 'flex', flexDirection: 'column', alignItems: 'center', justifyContent: 'center', gap: '4px' }}>
            <span style={{ fontWeight: '800', fontSize: '1.05rem', color: '#fff' }}>{value}</span>
            <span className={className}>{label}</span>
        </div>
    );
}

// --------------------------- REFERENCE FORMS ---------------------------

function renderReferenceForm(category, data, onChange, onSave) {
    switch (category) {
        case 'cabecote': return renderCabecoteRef(data, onChange, onSave);
        case 'motor': return renderMotorRef(data, onChange, onSave);
        case 'virabrequim': return renderVirabrequimRef(data, onChange, onSave);
        case 'embreagem': return renderEmbreagemRef(data, onChange, onSave);
        case 'bomba': return renderBombaRef(data, onChange, onSave);
        default: return null;
    }
}

function renderCabecoteRef(data, onChange, onSave) {
    return (
        <div className="form-container">
            <div className="data-card">
                <div className="data-card-title"><i className="fas fa-microchip"></i> Configuração do Motor</div>
                <div className="form-grid form-grid-3">
                    <div className="field-group"><label>Nº Cilindros</label><input type="number" className="field-input" value={data.num_cilindros || ''} onChange={e => onChange('reference', 'cabecote', 'num_cilindros', e.target.value)} /></div>
                    <div className="field-group"><label>Válvulas ADM/Cil</label><input type="number" className="field-input" value={data.num_val_adm || ''} onChange={e => onChange('reference', 'cabecote', 'num_val_adm', e.target.value)} /></div>
                    <div className="field-group"><label>Válvulas ESC/Cil</label><input type="number" className="field-input" value={data.num_val_esc || ''} onChange={e => onChange('reference', 'cabecote', 'num_val_esc', e.target.value)} /></div>
                </div>
                <div className="form-grid form-grid-2" style={{ marginTop: '25px' }}>
                    <div className="field-group">
                        <label>Disposição do Motor</label>
                        <div className="radio-group">
                            {['boxer', 'v', 'em linha'].map(v => (
                                <label key={v} className="radio-item"><input type="radio" value={v} checked={data.engine_type === v} onChange={() => onChange('reference', 'cabecote', 'engine_type', v)} /> {v.toUpperCase()}</label>
                            ))}
                        </div>
                    </div>
                    <div className="field-group">
                        <label>Comando de Válvulas</label>
                        <div className="radio-group">
                            {['vareta', 'dohc', 'ohc'].map(v => (
                                <label key={v} className="radio-item"><input type="radio" value={v} checked={data.valve_type === v} onChange={() => onChange('reference', 'cabecote', 'valve_type', v)} /> {v.toUpperCase()}</label>
                            ))}
                        </div>
                    </div>
                </div>
            </div>

            <div className="data-card">
                <div className="data-card-title"><i className="fas fa-ruler-vertical"></i> LIMITES DE FOLGA (mm)</div>
                <div className="form-grid form-grid-2">
                    <div className="field-group">
                        <label>Folga ADM (Mín / Máx)</label>
                        <div style={{ display: 'flex', gap: '15px' }}>
                            <input className="field-input" placeholder="Mín" value={data.val_adm_limite_min || ''} onChange={e => onChange('reference', 'cabecote', 'val_adm_limite_min', e.target.value)} />
                            <input className="field-input" placeholder="Máx" value={data.val_adm_limite_max || ''} onChange={e => onChange('reference', 'cabecote', 'val_adm_limite_max', e.target.value)} />
                        </div>
                    </div>
                    <div className="field-group">
                        <label>Folga ESC (Mín / Máx)</label>
                        <div style={{ display: 'flex', gap: '15px' }}>
                            <input className="field-input" placeholder="Mín" value={data.val_esc_limite_min || ''} onChange={e => onChange('reference', 'cabecote', 'val_esc_limite_min', e.target.value)} />
                            <input className="field-input" placeholder="Máx" value={data.val_esc_limite_max || ''} onChange={e => onChange('reference', 'cabecote', 'val_esc_limite_max', e.target.value)} />
                        </div>
                    </div>
                </div>
            </div>

            <div className="data-card">
                <div className="data-card-title"><i className="fas fa-compress-arrows-alt"></i> Outras Especificações</div>
                <div className="form-grid form-grid-2">
                    <div className="field-group"><label>Came ADM Mín (mm)</label><input className="field-input" value={data.came_adm_altura_min || ''} onChange={e => onChange('reference', 'cabecote', 'came_adm_altura_min', e.target.value)} /></div>
                    <div className="field-group"><label>Came ESC Mín (mm)</label><input className="field-input" value={data.came_esc_altura_min || ''} onChange={e => onChange('reference', 'cabecote', 'came_esc_altura_min', e.target.value)} /></div>
                    <div className="field-group">
                        <label>Compressão (Mín / Máx)</label>
                        <div style={{ display: 'flex', gap: '15px' }}>
                            <input className="field-input" placeholder="Mín" value={data.compressao_min || ''} onChange={e => onChange('reference', 'cabecote', 'compressao_min', e.target.value)} />
                            <input className="field-input" placeholder="Máx" value={data.compressao_max || ''} onChange={e => onChange('reference', 'cabecote', 'compressao_max', e.target.value)} />
                        </div>
                    </div>
                    <div className="field-group" style={{ display: 'flex', alignItems: 'center', marginTop: '30px' }}>
                        <label className="radio-item"><input type="checkbox" checked={!!data.tucho} onChange={e => onChange('reference', 'cabecote', 'tucho', e.target.checked ? 1 : 0)} /> <strong>USA PASTILHAS / TUCHO MECÂNICO</strong></label>
                    </div>
                </div>
            </div>
            <button className="primary-btn" onClick={onSave}><i className="fas fa-save"></i> SALVAR REFERÊNCIAS DO CABEÇOTE</button>
        </div>
    );
}

function renderMotorRef(data, onChange, onSave) {
    return (
        <div className="form-container">
            <div className="data-card">
                <div className="data-card-title"><i className="fas fa-cog"></i> Cilindros e Pistões</div>
                <div className="form-grid form-grid-2">
                    <div className="field-group"><label>Nº Cilindros</label><input type="number" className="field-input" value={data.nr_cilindros || ''} onChange={e => onChange('reference', 'motor', 'nr_cilindros', e.target.value)} /></div>
                    <div className="field-group"><label>Curso Pistão (mm)</label><input className="field-input" value={data.curso_pistao || ''} onChange={e => onChange('reference', 'motor', 'curso_pistao', e.target.value)} /></div>
                    <div className="field-group"><label>Diâm Cilindro Max</label><input className="field-input" value={data.diametro_cilindro_max || ''} onChange={e => onChange('reference', 'motor', 'diametro_cilindro_max', e.target.value)} /></div>
                    <div className="field-group"><label>Diâm Pistão Min</label><input className="field-input" value={data.diametro_pistao_min || ''} onChange={e => onChange('reference', 'motor', 'diametro_pistao_min', e.target.value)} /></div>
                    <div className="field-group"><label>Conicidade Max</label><input className="field-input" value={data.conicidade_max || ''} onChange={e => onChange('reference', 'motor', 'conicidade_max', e.target.value)} /></div>
                    <div className="field-group"><label>Ovalização Max</label><input className="field-input" value={data.ovalizacao_max || ''} onChange={e => onChange('reference', 'motor', 'ovalizacao_max', e.target.value)} /></div>
                </div>
            </div>
            <div className="data-card">
                <div className="data-card-title"><i className="fas fa-ring"></i> Anéis e Pino</div>
                <div className="form-grid form-grid-2">
                    <div className="field-group"><label>Abertura Anel 1 Max</label><input className="field-input" value={data.aber_anel_1_max || ''} onChange={e => onChange('reference', 'motor', 'aber_anel_1_max', e.target.value)} /></div>
                    <div className="field-group"><label>Abertura Anel 2 Max</label><input className="field-input" value={data.aber_anel_2_max || ''} onChange={e => onChange('reference', 'motor', 'aber_anel_2_max', e.target.value)} /></div>
                    <div className="field-group"><label>Diâm Pino Min</label><input className="field-input" value={data.dia_pino_pis_min || ''} onChange={e => onChange('reference', 'motor', 'dia_pino_pis_min', e.target.value)} /></div>
                    <div className="field-group"><label>Folga Pino Max</label><input className="field-input" value={data.folga_pino_pis_max || ''} onChange={e => onChange('reference', 'motor', 'folga_pino_pis_max', e.target.value)} /></div>
                </div>
            </div>
            <button className="primary-btn" onClick={onSave}><i className="fas fa-save"></i> SALVAR REFERÊNCIAS DO MOTOR</button>
        </div>
    );
}

function renderVirabrequimRef(data, onChange, onSave) {
    return (
        <div className="form-container">
            <div className="data-card">
                <div className="data-card-title"><i className="fas fa-sync-alt"></i> Tipo de Montagem</div>
                <div className="radio-group" style={{ marginBottom: '25px' }}>
                    {['rolamento', 'bronzina'].map(v => (
                        <label key={v} className="radio-item"><input type="radio" value={v} checked={data.rolamento_type === v} onChange={() => onChange('reference', 'virabrequim', 'rolamento_type', v)} /> {v.toUpperCase()}</label>
                    ))}
                </div>
                <div className="form-grid form-grid-3">
                    <div className="field-group"><label>Nº Moentes (Cil)</label><input type="number" className="field-input" value={data.qtd_cilindros || ''} onChange={e => onChange('reference', 'virabrequim', 'qtd_cilindros', e.target.value)} /></div>
                    <div className="field-group"><label>Nº Munhões</label><input type="number" className="field-input" value={data.qtd_munhoes || ''} onChange={e => onChange('reference', 'virabrequim', 'qtd_munhoes', e.target.value)} /></div>
                    <div className="field-group"><label>Folga Lat Biela Max</label><input className="field-input" value={data.folga_lateral_biela || ''} onChange={e => onChange('reference', 'virabrequim', 'folga_lateral_biela', e.target.value)} /></div>
                </div>
            </div>
            {data.rolamento_type === 'bronzina' && (
                <div className="data-card">
                    <div className="data-card-title"><i className="fas fa-arrows-alt-h"></i> Diâmetros de Projeto</div>
                    <div className="form-grid form-grid-2">
                        <div className="field-group"><label>Diâm Moente Ref (mm)</label><input className="field-input" value={data.diametro_moente || ''} onChange={e => onChange('reference', 'virabrequim', 'diametro_moente', e.target.value)} /></div>
                        <div className="field-group"><label>Diâm Munhão Ref (mm)</label><input className="field-input" value={data.diametro_munhao || ''} onChange={e => onChange('reference', 'virabrequim', 'diametro_munhao', e.target.value)} /></div>
                        <div className="field-group"><label>Folga Bronzina Max (mm)</label><input className="field-input" value={data.folga_biela || ''} onChange={e => onChange('reference', 'virabrequim', 'folga_biela', e.target.value)} /></div>
                        <div className="field-group"><label>Folga Mancal Max (mm)</label><input className="field-input" value={data.folga_mancal || ''} onChange={e => onChange('reference', 'virabrequim', 'folga_mancal', e.target.value)} /></div>
                    </div>
                </div>
            )}
            <div className="data-card">
                <div className="data-card-title"><i className="fas fa-compress-arrows-alt"></i> Empenamento</div>
                <div className="field-group"><label>Empenamento Eixo Max (mm)</label><input className="field-input" value={data.empenamento || ''} onChange={e => onChange('reference', 'virabrequim', 'empenamento', e.target.value)} /></div>
            </div>
            <button className="primary-btn" onClick={onSave}><i className="fas fa-save"></i> SALVAR REFERÊNCIAS DO VIRABREQUIM</button>
        </div>
    );
}

function renderEmbreagemRef(data, onChange, onSave) {
    return (
        <div className="form-container">
            <div className="data-card">
                <div className="data-card-title"><i className="fas fa-circle-notch"></i> Discos e Separadores</div>
                <div className="form-grid form-grid-2">
                    <div className="field-group"><label>Qtd Discos Fricção</label><input type="number" className="field-input" value={data.nr_discos || ''} onChange={e => onChange('reference', 'embreagem', 'nr_discos', e.target.value)} /></div>
                    <div className="field-group"><label>Qtd Separadores</label><input type="number" className="field-input" value={data.nr_discos_sep || ''} onChange={e => onChange('reference', 'embreagem', 'nr_discos_sep', e.target.value)} /></div>
                    <div className="field-group"><label>Fricção Espessura Mín</label><input className="field-input" value={data.disco_fric_esp_min || ''} onChange={e => onChange('reference', 'embreagem', 'disco_fric_esp_min', e.target.value)} /></div>
                    <div className="field-group"><label>Separador Empenamento Máx</label><input className="field-input" value={data.disco_sep_emp_max || ''} onChange={e => onChange('reference', 'embreagem', 'disco_sep_emp_max', e.target.value)} /></div>
                </div>
            </div>
            <button className="primary-btn" onClick={onSave}><i className="fas fa-save"></i> SALVAR REFERÊNCIAS DA EMBREAGEM</button>
        </div>
    );
}

function renderBombaRef(data, onChange, onSave) {
    return (
        <div className="form-container">
            <div className="data-card">
                <div className="data-card-title"><i className="fas fa-tint"></i> Bomba de Óleo</div>
                <div className="form-grid form-grid-2">
                    <div className="field-group"><label>Pressão Óleo Mín</label><input className="field-input" value={data.pressao_oleo_min || ''} onChange={e => onChange('reference', 'bomba', 'pressao_oleo_min', e.target.value)} /></div>
                    <div className="field-group"><label>Pressão Óleo Máx</label><input className="field-input" value={data.pressao_oleo_max || ''} onChange={e => onChange('reference', 'bomba', 'pressao_oleo_max', e.target.value)} /></div>
                </div>
            </div>
            <div className="data-card">
                <div className="data-card-title"><i className="fas fa-gas-pump"></i> Bomba de Combustível</div>
                <div className="form-grid form-grid-3">
                    <div className="field-group"><label>Pressão Trabalho</label><input className="field-input" value={data.pressao_combustao || ''} onChange={e => onChange('reference', 'bomba', 'pressao_combustao', e.target.value)} /></div>
                    <div className="field-group"><label>Vazão Mín</label><input className="field-input" value={data.vazao_combustao_min || ''} onChange={e => onChange('reference', 'bomba', 'vazao_combustao_min', e.target.value)} /></div>
                    <div className="field-group"><label>Vazão Máx</label><input className="field-input" value={data.vazao_combustao_max || ''} onChange={e => onChange('reference', 'bomba', 'vazao_combustao_max', e.target.value)} /></div>
                </div>
            </div>
            <button className="primary-btn" onClick={onSave}><i className="fas fa-save"></i> SALVAR REFERÊNCIAS DAS BOMBAS</button>
        </div>
    );
}

// --------------------------- MEASUREMENT FORMS (DADOS) ---------------------------

function renderMeasurementForm(category, ref, med, onChange, onSave, calcShim) {
    if (!ref || Object.keys(ref).length === 0) return <div style={{ textAlign: 'center', padding: '100px', color: '#666', fontSize: '1.2rem', fontWeight: 700 }}>⚠️ Defina as REFERÊNCIAS antes de iniciar as medições.</div>;
    switch (category) {
        case 'cabecote': return renderCabecoteMed(ref, med, onChange, onSave, calcShim);
        case 'motor': return renderMotorMed(ref, med, onChange, onSave);
        case 'virabrequim': return renderVirabrequimMed(ref, med, onChange, onSave);
        case 'embreagem': return renderEmbreagemMed(ref, med, onChange, onSave);
        case 'bomba': return renderBombaMed(ref, med, onChange, onSave);
        default: return null;
    }
}

function renderCabecoteMed(ref, med, onChange, onSave, calcShim) {
    const numCil = parseInt(ref.num_cilindros) || 0;
    const numAdm = parseInt(ref.num_val_adm) || 0;
    const numEsc = parseInt(ref.num_val_esc) || 0;
    const cilArr = Array.from({ length: numCil }, (_, i) => i + 1);
    const cilTras = Math.ceil(numCil / 2);
    const cilFront = numCil - cilTras;

    return (
        <div className="form-container">
            <div style={{ overflowX: 'auto', marginBottom: '30px' }}>
                <table className="medicoes-table">
                    <thead>
                        {numCil > 1 && (
                            <tr><th></th><th></th><th colSpan={cilTras} className="back-heading">CABEÇOTE TRASEIRO / ESQUERDO</th>{cilFront > 0 && <th colSpan={cilFront} className="front-heading">CABEÇOTE DIANTEIRO / DIREITO</th>}</tr>
                        )}
                        <tr><th>ITEM DE MEDIÇÃO</th><th>REF. FÁBRICA</th>{cilArr.map(c => <th key={c}>CIL {c}</th>)}</tr>
                    </thead>
                    <tbody>
                        {Array.from({ length: numAdm }).map((_, i) => {
                            const lado = i === 0 ? 'direita' : 'esquerda';
                            return (
                                <tr key={`af-${lado}`} className="valvula-admissao">
                                    <td style={{ fontWeight: 700 }}>Folga ADM ({lado})</td><td>{renderRefDisplay(`${ref.val_adm_limite_min}-${ref.val_adm_limite_max}`, 'RANGE')}</td>
                                    {cilArr.map(c => <td key={c}><input className="meas-field" value={med[`adm_folga_${lado}`]?.[c] || ''} onChange={e => onChange('measurement', 'cabecote', `adm_folga_${lado}`, e.target.value, c)} /></td>)}
                                </tr>
                            );
                        })}
                        {!!ref.tucho && Array.from({ length: numAdm }).map((_, i) => {
                            const lado = i === 0 ? 'direita' : 'esquerda';
                            return (
                                <tr key={`ap-${lado}`} className="valvula-admissao">
                                    <td style={{ fontWeight: 700 }}>Pastilha ADM ({lado})</td><td>{renderRefDisplay('ORIGINAL', 'REF')}</td>
                                    {cilArr.map(c => {
                                        const corr = calcShim('adm', lado, c);
                                        return <td key={c}><input className="meas-field" value={med[`adm_pastilha_${lado}`]?.[c] || ''} onChange={e => onChange('measurement', 'cabecote', `adm_pastilha_${lado}`, e.target.value, c)} />{corr && <div className="shim-display">IDEAL: {corr}</div>}</td>;
                                    })}
                                </tr>
                            );
                        })}
                        {Array.from({ length: numEsc }).map((_, i) => {
                            const lado = i === 0 ? 'direita' : 'esquerda';
                            return (
                                <tr key={`ef-${lado}`} className="valvula-escape">
                                    <td style={{ fontWeight: 700 }}>Folga ESC ({lado})</td><td>{renderRefDisplay(`${ref.val_esc_limite_min}-${ref.val_esc_limite_max}`, 'RANGE')}</td>
                                    {cilArr.map(c => <td key={c}><input className="meas-field" value={med[`esc_folga_${lado}`]?.[c] || ''} onChange={e => onChange('measurement', 'cabecote', `esc_folga_${lado}`, e.target.value, c)} /></td>)}
                                </tr>
                            );
                        })}
                        {!!ref.tucho && Array.from({ length: numEsc }).map((_, i) => {
                            const lado = i === 0 ? 'direita' : 'esquerda';
                            return (
                                <tr key={`ep-${lado}`} className="valvula-escape">
                                    <td style={{ fontWeight: 700 }}>Pastilha ESC ({lado})</td><td>{renderRefDisplay('ORIGINAL', 'REF')}</td>
                                    {cilArr.map(c => {
                                        const corr = calcShim('esc', lado, c);
                                        return <td key={c}><input className="meas-field" value={med[`esc_pastilha_${lado}`]?.[c] || ''} onChange={e => onChange('measurement', 'cabecote', `esc_pastilha_${lado}`, e.target.value, c)} />{corr && <div className="shim-display">IDEAL: {corr}</div>}</td>;
                                    })}
                                </tr>
                            );
                        })}
                        <tr className="group-heading"><td style={{ fontWeight: 800 }}>Compressão Motor</td><td>{renderRefDisplay(`${ref.compressao_min}-${ref.compressao_max}`, 'RANGE')}</td>{cilArr.map(c => <td key={c}><input className="meas-field" value={med.compressao?.[c] || ''} onChange={e => onChange('measurement', 'cabecote', 'compressao', e.target.value, c)} /></td>)}</tr>
                    </tbody>
                </table>
            </div>
            <button className="primary-btn" onClick={onSave} style={{ background: '#00c063' }}><i className="fas fa-check-circle"></i> SALVAR MEDIÇÕES DO CABEÇOTE</button>
        </div>
    );
}

function renderMotorMed(ref, med, onChange, onSave) {
    const numCil = parseInt(ref.nr_cilindros) || 0;
    const cilArr = Array.from({ length: numCil }, (_, i) => i + 1);
    const fields = [
        { id: 'diametro_cilindro', label: 'Diâm Cilindro', ref: 'diametro_cilindro_max', suffix: 'MAX' },
        { id: 'diametro_pistao', label: 'Diâm Pistão', ref: 'diametro_pistao_min', suffix: 'MIN' },
        { id: 'conicidade', label: 'Conicidade', ref: 'conicidade_max', suffix: 'MAX' },
        { id: 'ovalizacao', label: 'Ovalização', ref: 'ovalizacao_max', suffix: 'MAX' },
        { id: 'aber_anel_1', label: 'Abert. Anel 1', ref: 'aber_anel_1_max', suffix: 'MAX' },
        { id: 'aber_anel_2', label: 'Abert. Anel 2', ref: 'aber_anel_2_max', suffix: 'MAX' }
    ];

    return (
        <div className="form-container">
            <div style={{ overflowX: 'auto', marginBottom: '30px' }}>
                <table className="medicoes-table">
                    <thead><tr><th>ITEM</th><th>REFERÊNCIA</th>{cilArr.map(c => <th key={c}>CIL {c}</th>)}</tr></thead>
                    <tbody>
                        {fields.map(f => (
                            <tr key={f.id}>
                                <td style={{ fontWeight: 700 }}>{f.label}</td><td>{renderRefDisplay(ref[f.ref], f.suffix)}</td>
                                {cilArr.map(c => <td key={c}><input className="meas-field" value={med[f.id]?.[c] || ''} onChange={e => onChange('measurement', 'motor', f.id, e.target.value, c)} /></td>)}
                            </tr>
                        ))}
                    </tbody>
                </table>
            </div>
            <button className="primary-btn" onClick={onSave} style={{ background: '#00c063' }}><i className="fas fa-check-circle"></i> SALVAR MEDIÇÕES DO MOTOR</button>
        </div>
    );
}

function renderVirabrequimMed(ref, med, onChange, onSave) {
    const numCil = parseInt(ref.qtd_cilindros) || 0;
    const numMun = parseInt(ref.qtd_munhoes) || 0;
    const maxCols = Math.max(numCil, numMun);
    const colArr = Array.from({ length: maxCols }, (_, i) => i + 1);
    const isBronzina = ref.rolamento_type === 'bronzina';

    return (
        <div className="form-container">
            <div style={{ overflowX: 'auto', marginBottom: '30px' }}>
                <table className="medicoes-table">
                    <thead>
                        <tr className="group-heading"><th colSpan={maxCols + 2}>MEDIÇÕES DE DIÂMETROS E FOLGAS</th></tr>
                        <tr><th>ITEM</th><th>REFERÊNCIA</th>{colArr.map(c => <th key={c}>{c}</th>)}</tr>
                    </thead>
                    <tbody>
                        {isBronzina && (
                            <>
                                <tr>
                                    <td style={{ fontWeight: 700 }}>Diâm Moente</td><td>{renderRefDisplay(ref.diametro_moente, 'REF')}</td>
                                    {colArr.map(c => <td key={c}>{c <= numCil ? <input className="meas-field" value={med.diametro_moente?.[c] || ''} onChange={e => onChange('measurement', 'virabrequim', 'diametro_moente', e.target.value, c)} /> : '-'}</td>)}
                                </tr>
                                <tr>
                                    <td style={{ fontWeight: 700 }}>Diâm Munhão</td><td>{renderRefDisplay(ref.diametro_munhao, 'REF')}</td>
                                    {colArr.map(c => <td key={c}>{c <= numMun ? <input className="meas-field" value={med.diametro_munhao?.[c] || ''} onChange={e => onChange('measurement', 'virabrequim', 'diametro_munhao', e.target.value, c)} /> : '-'}</td>)}
                                </tr>
                                <tr>
                                    <td style={{ fontWeight: 700 }}>Folga Mancal</td><td>{renderRefDisplay(ref.folga_mancal, 'MAX')}</td>
                                    {colArr.map(c => <td key={c}>{c <= numMun ? <input className="meas-field" value={med.folga_mancal?.[c] || ''} onChange={e => onChange('measurement', 'virabrequim', 'folga_mancal', e.target.value, c)} /> : '-'}</td>)}
                                </tr>
                            </>
                        )}
                        <tr>
                            <td style={{ fontWeight: 700 }}>{isBronzina ? 'Folga Bronzina' : 'Folga Biela'}</td><td>{renderRefDisplay(ref.folga_biela, 'MAX')}</td>
                            {colArr.map(c => <td key={c}>{c <= numCil ? <input className="meas-field" value={med.folga_biela?.[c] || ''} onChange={e => onChange('measurement', 'virabrequim', 'folga_biela', e.target.value, c)} /> : '-'}</td>)}
                        </tr>
                        <tr>
                            <td style={{ fontWeight: 700 }}>Empenamento</td><td>{renderRefDisplay(ref.empenamento, 'MAX')}</td>
                            {colArr.map(c => <td key={c}>{c <= numCil ? <input className="meas-field" value={med.empenamento?.[c] || ''} onChange={e => onChange('measurement', 'virabrequim', 'empenamento', e.target.value, c)} /> : '-'}</td>)}
                        </tr>
                    </tbody>
                </table>
            </div>
            <button className="primary-btn" onClick={onSave} style={{ background: '#00c063' }}><i className="fas fa-check-circle"></i> SALVAR MEDIÇÕES DO VIRABREQUIM</button>
        </div>
    );
}

function renderEmbreagemMed(ref, med, onChange, onSave) {
    return (
        <div className="form-container">
            <div className="data-card">
                <div className="data-card-title"><i className="fas fa-microscope"></i> MEDIÇÕES REAIS</div>
                <div className="form-grid form-grid-2">
                    <div className="field-group"><label>Fricção Espessura Medida</label><input className="field-input" value={med.disco_fric_esp || ''} onChange={e => onChange('measurement', 'embreagem', 'disco_fric_esp', e.target.value)} /></div>
                    <div className="field-group"><label>Separador Empenamento Medido</label><input className="field-input" value={med.disco_sep_emp || ''} onChange={e => onChange('measurement', 'embreagem', 'disco_sep_emp', e.target.value)} /></div>
                </div>
            </div>
            <button className="primary-btn" onClick={onSave} style={{ background: '#00c063' }}><i className="fas fa-check-circle"></i> SALVAR MEDIÇÕES DA EMBREAGEM</button>
        </div>
    );
}

function renderBombaMed(ref, med, onChange, onSave) {
    return (
        <div className="form-container">
            <div className="data-card">
                <div className="data-card-title"><i className="fas fa-clock"></i> TESTES DE PRESSÃO E VAZÃO</div>
                <div className="form-grid form-grid-2">
                    <div className="field-group"><label>Pressão Óleo</label><input className="field-input" value={med.pressao_oleo || ''} onChange={e => onChange('measurement', 'bomba', 'pressao_oleo', e.target.value)} /></div>
                    <div className="field-group"><label>Pressão Combustível</label><input className="field-input" value={med.pressao_combustao || ''} onChange={e => onChange('measurement', 'bomba', 'pressao_combustao', e.target.value)} /></div>
                    <div className="field-group"><label>Vazão Combustível</label><input className="field-input" value={med.vazao_combustao || ''} onChange={e => onChange('measurement', 'bomba', 'vazao_combustao', e.target.value)} /></div>
                </div>
            </div>
            <button className="primary-btn" onClick={onSave} style={{ background: '#00c063' }}><i className="fas fa-check-circle"></i> SALVAR MEDIÇÕES DAS BOMBAS</button>
        </div>
    );
}
