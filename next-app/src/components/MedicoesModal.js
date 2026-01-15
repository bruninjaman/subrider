'use client';

import { useState } from 'react';

export default function MedicoesModal({ isOpen, onClose, id, measurements, references }) {
    const [currentPage, setCurrentPage] = useState('choice');
    const [formData, setFormData] = useState({});

    if (!isOpen) return null;

    const categories = [
        { id: 'cabecote', label: 'Cabeçote', icon: '🔧' },
        { id: 'motor', label: 'Motor', icon: '⚙️' },
        { id: 'virabrequim', label: 'Virabrequim', icon: '🔩' },
        { id: 'embreagem', label: 'Embreagem', icon: '🔄' },
        { id: 'bomba', label: 'Bombas', icon: '⛽' }
    ];

    const handleBack = () => setCurrentPage('choice');

    const handleChange = (e) => {
        const { name, value, type, checked } = e.target;
        setFormData({
            ...formData,
            [name]: type === 'checkbox' ? (checked ? 1 : 0) : value
        });
    };

    const handleSubmit = async (e) => {
        e.preventDefault();
        try {
            const res = await fetch(`/api/ordemservico/measurements/${id}`, {
                method: 'POST',
                body: JSON.stringify({
                    table: currentPage,
                    measurements: formData
                }),
                headers: { 'Content-Type': 'application/json' }
            });
            if (res.ok) {
                alert(`Medições de ${currentPage} salvas com sucesso!`);
                onClose();
                window.location.reload(); // Refresh to show new data in report
            } else {
                throw new Error('Erro ao salvar');
            }
        } catch (err) {
            alert('Erro ao salvar medições: ' + err.message);
        }
    };

    return (
        <div className="modal-overlay" onClick={onClose}>
            <div className="modal-content" onClick={(e) => e.stopPropagation()} style={{ maxWidth: '900px', width: '95%' }}>
                <button className="close-modal" onClick={onClose}>&times;</button>

                {currentPage === 'choice' && (
                    <div style={{ padding: '20px' }}>
                        <h1 style={{
                            textAlign: 'center',
                            marginBottom: '40px',
                            background: 'linear-gradient(90deg, #181921 0%, #2c2d34 100%)',
                            padding: '25px',
                            borderRadius: '12px',
                            boxShadow: '0 4px 15px rgba(0,0,0,0.3)',
                            color: '#e44c65',
                            fontSize: '2.2rem'
                        }}>Menu Medições</h1>

                        <div style={{ display: 'grid', gridTemplateColumns: 'repeat(auto-fit, minmax(200px, 1fr))', gap: '25px' }}>
                            {categories.map(cat => (
                                <button
                                    key={cat.id}
                                    className="button secondary"
                                    style={{
                                        padding: '40px 20px',
                                        fontSize: '1.2rem',
                                        display: 'flex',
                                        flexDirection: 'column',
                                        alignItems: 'center',
                                        gap: '20px',
                                        borderRadius: '15px',
                                        border: '1px solid rgba(255,255,255,0.1)',
                                        background: 'rgba(255,255,255,0.03)',
                                        transition: 'all 0.3s'
                                    }}
                                    onClick={() => setCurrentPage(cat.id)}
                                    onMouseOver={(e) => e.currentTarget.style.background = 'rgba(228, 76, 101, 0.1)'}
                                    onMouseOut={(e) => e.currentTarget.style.background = 'rgba(255,255,255,0.03)'}
                                >
                                    <span style={{ fontSize: '3.5rem' }}>{cat.icon}</span>
                                    <span style={{ fontWeight: '500' }}>{cat.label}</span>
                                </button>
                            ))}
                        </div>
                    </div>
                )}

                {currentPage !== 'choice' && (
                    <div className="form-container" style={{ padding: '20px' }}>
                        <div style={{
                            display: 'flex',
                            justifyContent: 'space-between',
                            alignItems: 'center',
                            marginBottom: '30px',
                            background: '#181921',
                            padding: '15px 25px',
                            borderRadius: '12px'
                        }}>
                            <h2 style={{ margin: 0, color: '#e44c65' }}>Menu {categories.find(c => c.id === currentPage)?.label}</h2>
                            <button className="button secondary" style={{ padding: '8px 20px' }} onClick={handleBack}>← Voltar</button>
                        </div>

                        <form onSubmit={handleSubmit} style={{ maxHeight: '60vh', overflowY: 'auto', paddingRight: '15px' }}>

                            {currentPage === 'cabecote' && (
                                <div style={{ display: 'flex', flexDirection: 'column', gap: '30px' }}>
                                    <div style={{ display: 'grid', gridTemplateColumns: 'repeat(3, 1fr)', gap: '20px' }}>
                                        <div className="floating-input">
                                            <label style={{ color: '#888', fontSize: '0.85rem' }}>Nº Cilindros</label>
                                            <input type="number" name="num_cilindros" className="search-input" style={{ width: '100%' }} onChange={handleChange} />
                                        </div>
                                        <div className="floating-input">
                                            <label style={{ color: '#888', fontSize: '0.85rem' }}>Válv. Adm./Cil.</label>
                                            <input type="number" name="num_val_adm" className="search-input" style={{ width: '100%' }} onChange={handleChange} />
                                        </div>
                                        <div className="floating-input">
                                            <label style={{ color: '#888', fontSize: '0.85rem' }}>Válv. Esc./Cil.</label>
                                            <input type="number" name="num_val_esc" className="search-input" style={{ width: '100%' }} onChange={handleChange} />
                                        </div>
                                    </div>

                                    <div className="section-group">
                                        <h3 style={{ color: '#e44c65', fontSize: '1.2rem', marginBottom: '15px', borderBottom: '1px solid #333' }}>Configuração do Motor</h3>
                                        <div style={{ display: 'flex', gap: '20px' }}>
                                            {['Boxer', 'Em V', 'Em Linha'].map(type => (
                                                <label key={type} style={{ display: 'flex', alignItems: 'center', gap: '8px', cursor: 'pointer' }}>
                                                    <input type="radio" name="engine_type" value={type} onChange={handleChange} />
                                                    {type}
                                                </label>
                                            ))}
                                        </div>
                                    </div>

                                    <div className="section-group">
                                        <h3 style={{ color: '#e44c65', fontSize: '1.2rem', marginBottom: '15px', borderBottom: '1px solid #333' }}>Medidas e Limites</h3>
                                        <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: '25px' }}>
                                            <div>
                                                <p style={{ marginBottom: '10px', fontSize: '0.9rem', color: '#ccc' }}>Válvula Admissão Limite (mm)</p>
                                                <div style={{ display: 'flex', gap: '10px' }}>
                                                    <input type="text" name="val_adm_limite_min" className="search-input" placeholder="Mín" onChange={handleChange} />
                                                    <input type="text" name="val_adm_limite_max" className="search-input" placeholder="Máx" onChange={handleChange} />
                                                </div>
                                            </div>
                                            <div>
                                                <p style={{ marginBottom: '10px', fontSize: '0.9rem', color: '#ccc' }}>Válvula Escape Limite (mm)</p>
                                                <div style={{ display: 'flex', gap: '10px' }}>
                                                    <input type="text" name="val_esc_limite_min" className="search-input" placeholder="Mín" onChange={handleChange} />
                                                    <input type="text" name="val_esc_limite_max" className="search-input" placeholder="Máx" onChange={handleChange} />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            )}

                            {currentPage === 'bomba' && (
                                <div style={{ display: 'flex', flexDirection: 'column', gap: '30px' }}>
                                    <div className="section-group">
                                        <h3 style={{ color: '#e44c65', fontSize: '1.2rem', marginBottom: '15px', borderBottom: '1px solid #333' }}>Bomba de Óleo</h3>
                                        <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: '20px' }}>
                                            <div>
                                                <label style={{ color: '#888', fontSize: '0.85rem' }}>Pressão Mínima</label>
                                                <input type="text" name="pressao_oleo_min" className="search-input" style={{ width: '100%' }} onChange={handleChange} />
                                            </div>
                                            <div>
                                                <label style={{ color: '#888', fontSize: '0.85rem' }}>Pressão Máxima</label>
                                                <input type="text" name="pressao_oleo_max" className="search-input" style={{ width: '100%' }} onChange={handleChange} />
                                            </div>
                                        </div>
                                    </div>

                                    <div className="section-group">
                                        <h3 style={{ color: '#e44c65', fontSize: '1.2rem', marginBottom: '15px', borderBottom: '1px solid #333' }}>Bomba de Combustão</h3>
                                        <div style={{ display: 'flex', flexDirection: 'column', gap: '20px' }}>
                                            <div>
                                                <label style={{ color: '#888', fontSize: '0.85rem' }}>Pressão da Bomba</label>
                                                <input type="text" name="pressao_combustao" className="search-input" style={{ width: '100%' }} onChange={handleChange} />
                                            </div>
                                            <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: '20px' }}>
                                                <div>
                                                    <label style={{ color: '#888', fontSize: '0.85rem' }}>Vazão Mínima</label>
                                                    <input type="text" name="vazao_combustao_min" className="search-input" style={{ width: '100%' }} onChange={handleChange} />
                                                </div>
                                                <div>
                                                    <label style={{ color: '#888', fontSize: '0.85rem' }}>Vazão Máxima</label>
                                                    <input type="text" name="vazao_combustao_max" className="search-input" style={{ width: '100%' }} onChange={handleChange} />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            )}

                            {currentPage === 'motor' && (
                                <div style={{ display: 'flex', flexDirection: 'column', gap: '30px' }}>
                                    <div className="section-group">
                                        <h3 style={{ color: '#e44c65', fontSize: '1.2rem', marginBottom: '15px', borderBottom: '1px solid #333' }}>Medições do Motor</h3>
                                        <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: '20px' }}>
                                            <div>
                                                <label style={{ color: '#888', fontSize: '0.85rem' }}>Diâmetro Cilindro Máx</label>
                                                <input type="text" name="diametro_cilindro_max" className="search-input" style={{ width: '100%' }} onChange={handleChange} />
                                            </div>
                                            <div>
                                                <label style={{ color: '#888', fontSize: '0.85rem' }}>Pistão Diâmetro Mín</label>
                                                <input type="text" name="diametro_pistao_min" className="search-input" style={{ width: '100%' }} onChange={handleChange} />
                                            </div>
                                            <div>
                                                <label style={{ color: '#888', fontSize: '0.85rem' }}>Conicidade Máx</label>
                                                <input type="text" name="conicidade_max" className="search-input" style={{ width: '100%' }} onChange={handleChange} />
                                            </div>
                                            <div>
                                                <label style={{ color: '#888', fontSize: '0.85rem' }}>Ovalização Máx</label>
                                                <input type="text" name="ovalizacao_max" className="search-input" style={{ width: '100%' }} onChange={handleChange} />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            )}

                            {currentPage === 'virabrequim' && (
                                <div style={{ display: 'flex', flexDirection: 'column', gap: '30px' }}>
                                    <div className="section-group">
                                        <h3 style={{ color: '#e44c65', fontSize: '1.2rem', marginBottom: '15px', borderBottom: '1px solid #333' }}>Virabrequim</h3>
                                        <div style={{ marginBottom: '20px' }}>
                                            <p style={{ color: '#ccc', marginBottom: '10px' }}>Tipo de Virabrequim</p>
                                            <div style={{ display: 'flex', gap: '20px' }}>
                                                {['Rolamento', 'Bronzina'].map(t => (
                                                    <label key={t} style={{ display: 'flex', alignItems: 'center', gap: '8px', cursor: 'pointer' }}>
                                                        <input type="radio" name="tipo" value={t.toLowerCase()} onChange={handleChange} />
                                                        {t}
                                                    </label>
                                                ))}
                                            </div>
                                        </div>
                                        <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: '20px' }}>
                                            <div>
                                                <label style={{ color: '#888', fontSize: '0.85rem' }}>Folga Lateral Biela</label>
                                                <input type="text" name="folga_lateral_biela" className="search-input" style={{ width: '100%' }} onChange={handleChange} />
                                            </div>
                                            <div>
                                                <label style={{ color: '#888', fontSize: '0.85rem' }}>Empenamento</label>
                                                <input type="text" name="empenamento" className="search-input" style={{ width: '100%' }} onChange={handleChange} />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            )}

                            {currentPage === 'embreagem' && (
                                <div style={{ display: 'flex', flexDirection: 'column', gap: '30px' }}>
                                    <div className="section-group">
                                        <h3 style={{ color: '#e44c65', fontSize: '1.2rem', marginBottom: '15px', borderBottom: '1px solid #333' }}>Medições de Embreagem</h3>
                                        <div>
                                            <label style={{ color: '#888', fontSize: '0.85rem' }}>Espessura Disco Fricção Mín (Ref)</label>
                                            <input type="text" name="disco_friccao_espes_min" className="search-input" style={{ width: '100%', marginBottom: '10px' }} onChange={handleChange} />
                                            <label style={{ color: '#888', fontSize: '0.85rem' }}>Empenamento Disco Separador Máx (Ref)</label>
                                            <input type="text" name="disco_separador_emp_max" className="search-input" style={{ width: '100%' }} onChange={handleChange} />
                                        </div>
                                    </div>
                                </div>
                            )}

                            <div style={{ display: 'flex', gap: '20px', marginTop: '40px', justifyContent: 'center' }}>
                                <button type="submit" className="button primary" style={{ minWidth: '220px', padding: '15px' }}>SALVAR MEDIÇÕES</button>
                                <button type="button" className="button secondary" onClick={onClose} style={{ minWidth: '220px', padding: '15px' }}>CANCELAR</button>
                            </div>
                        </form>
                    </div>
                )}
            </div>
        </div>
    );
}
