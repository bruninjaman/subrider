'use client';

import { useState } from 'react';
import { formatNumber, isInRange, getValidationType } from '@/lib/utils';

export default function MeasurementsReport({ measurements, references, id }) {
    const [expanded, setExpanded] = useState({
        cabecote: false,
        motor: false,
        virabrequim: false,
        embreagem: false,
        bomba: false
    });

    const toggle = (table) => {
        setExpanded(prev => ({ ...prev, [table]: !prev[table] }));
    };

    const expandAll = () => {
        setExpanded({
            cabecote: true,
            motor: true,
            virabrequim: true,
            embreagem: true,
            bomba: true
        });
    };

    const collapseAll = () => {
        setExpanded({
            cabecote: false,
            motor: false,
            virabrequim: false,
            embreagem: false,
            bomba: false
        });
    };

    const handlePrint = () => {
        expandAll();
        setTimeout(() => {
            window.print();
        }, 300);
    };

    const components = [
        { key: 'cabecote', title: 'Cabeçote', icon: '🔧' },
        { key: 'motor', title: 'Motor', icon: '⚙️' },
        { key: 'virabrequim', title: 'Virabrequim', icon: '🔩' },
        { key: 'embreagem', title: 'Embreagem', icon: '🔄' },
        { key: 'bomba', title: 'Bomba', icon: '⛽' }
    ];

    const renderComponentTable = (tableKey, title, icon) => {
        const row = measurements[tableKey];
        const refData = references[tableKey];
        if (!row) return null;

        let content = [];
        let hasData = false;

        const parseMedicoes = (json) => {
            if (!json) return null;
            try { return typeof json === 'string' ? JSON.parse(json) : json; }
            catch (e) { return null; }
        };

        const medicoes = parseMedicoes(row.medicoes);

        switch (tableKey) {
            case 'bomba': {
                if (medicoes) {
                    const fields = {
                        pressao_oleo_min: 'Pressão Óleo Mín',
                        pressao_oleo_max: 'Pressão Óleo Máx',
                        vazao_min: 'Vazão Mín',
                        vazao_max: 'Vazão Máx',
                        comb_pressao: 'Pressão Combustível'
                    };
                    Object.entries(medicoes).forEach(([campo, valor]) => {
                        const formatted = formatNumber(valor);
                        if (formatted !== null) {
                            const label = fields[campo] || campo;
                            const refVal = refData?.[campo];
                            const inRangeResult = isInRange(valor, refVal, getValidationType(campo, 'bomba'));
                            content.push(
                                <tr key={campo}>
                                    <td style={{ color: 'rgba(255,255,255,0.7)' }}>{label}</td>
                                    <td className="reference-value" style={{ fontStyle: 'italic', opacity: 0.5 }}>{refVal ? `${formatNumber(refVal)} mm` : '-'}</td>
                                    <td className={refVal ? (inRangeResult ? 'in-range' : 'out-range') : ''} style={{ fontWeight: 'bold', textAlign: 'center' }}>
                                        {formatted} {refVal ? 'mm' : ''}
                                    </td>
                                </tr>
                            );
                            hasData = true;
                        }
                    });
                }
                break;
            }

            case 'embreagem': {
                const medicoesFriccao = parseMedicoes(row.medicoes_friccao);
                const medicoesSeparador = parseMedicoes(row.medicoes_separador);

                if (medicoesFriccao) {
                    const refMin = refData?.disco_friccao_espes_min;
                    medicoesFriccao.forEach((valor, i) => {
                        const formatted = formatNumber(valor);
                        if (formatted) {
                            const inRangeResult = isInRange(valor, refMin, 'min');
                            content.push(
                                <tr key={`fric-${i}`}>
                                    <td style={{ color: 'rgba(255,255,255,0.7)' }}>Disco Fricção {i + 1}</td>
                                    <td style={{ fontStyle: 'italic', opacity: 0.5 }}>{refMin ? `${formatNumber(refMin)} mm (mín)` : '-'}</td>
                                    <td className={refMin ? (inRangeResult ? 'in-range' : 'out-range') : ''} style={{ fontWeight: 'bold', textAlign: 'center' }}>{formatted} mm</td>
                                </tr>
                            );
                            hasData = true;
                        }
                    });
                }
                if (medicoesSeparador) {
                    const refMax = refData?.disco_separador_emp_max;
                    medicoesSeparador.forEach((valor, i) => {
                        const formatted = formatNumber(valor);
                        if (formatted) {
                            const inRangeResult = isInRange(valor, refMax, 'max');
                            content.push(
                                <tr key={`sep-${i}`}>
                                    <td style={{ color: 'rgba(255,255,255,0.7)' }}>Disco Separador {i + 1}</td>
                                    <td style={{ fontStyle: 'italic', opacity: 0.5 }}>{refMax ? `${formatNumber(refMax)} mm (máx)` : '-'}</td>
                                    <td className={refMax ? (inRangeResult ? 'in-range' : 'out-range') : ''} style={{ fontWeight: 'bold', textAlign: 'center' }}>{formatted} mm</td>
                                </tr>
                            );
                            hasData = true;
                        }
                    });
                }
                break;
            }

            case 'motor': {
                if (medicoes) {
                    const fields = {
                        curso_pistao: 'Curso Pistão',
                        diametro_cilindro_max: 'Diâm. Cilindro Máx',
                        conicidade_max: 'Conicidade Máx',
                        ovalizacao_max: 'Ovalização Máx',
                        diametro_pistao_min: 'Diâm. Pistão Mín',
                        folga_cil_pis_max: 'Folga Cil/Pis Máx',
                        aber_anel_1_max: 'Aber. Anel 1 Máx',
                        aber_anel_2_max: 'Aber. Anel 2 Máx',
                        aber_anel_1_pres_min: 'Press. Anel 1 Mín',
                        aber_anel_2_pres_min: 'Press. Anel 2 Mín'
                    };
                    Object.entries(medicoes).sort().forEach(([cilindro, dados]) => {
                        if (dados && typeof dados === 'object') {
                            content.push(
                                <tr key={`cyl-${cilindro}`} style={{ background: 'rgba(228, 76, 101, 0.05)' }}>
                                    <td colSpan="3" style={{ textAlign: 'center', fontWeight: 'bold', color: '#e44c65', letterSpacing: '2px', padding: '10px' }}>CILINDRO {cilindro}</td>
                                </tr>
                            );
                            Object.entries(dados).forEach(([campo, valor]) => {
                                const formatted = formatNumber(valor);
                                if (formatted) {
                                    const refVal = refData?.[campo];
                                    const inRangeResult = isInRange(valor, refVal, getValidationType(campo, 'motor'));
                                    content.push(
                                        <tr key={`${cilindro}-${campo}`}>
                                            <td style={{ color: 'rgba(255,255,255,0.7)' }}>{fields[campo] || campo}</td>
                                            <td style={{ fontStyle: 'italic', opacity: 0.5 }}>{refVal ? `${formatNumber(refVal)} mm` : '-'}</td>
                                            <td className={refVal ? (inRangeResult ? 'in-range' : 'out-range') : ''} style={{ fontWeight: 'bold', textAlign: 'center' }}>{formatted} mm</td>
                                        </tr>
                                    );
                                    hasData = true;
                                }
                            });
                        }
                    });
                }
                break;
            }

            case 'virabrequim': {
                if (medicoes) {
                    const tipo = refData?.tipo?.toLowerCase() || '';
                    const fields = {
                        folga_mancal: tipo === 'rolamento' ? 'Folga Eixo Mancal' : 'Folga Mancal',
                        folga_biela: tipo === 'rolamento' ? 'Folga Biela' : 'Folga Bronzina',
                        folga_bronzina: 'Folga Bronzina',
                        folga_lateral_biela: 'Folga Lat. Biela',
                        folga_lateral_eixo_min: 'Folga Lat. Eixo Mín',
                        folga_lateral_eixo_max: 'Folga Lat. Eixo Máx',
                        empenamento: 'Empenamento'
                    };

                    // Header for basic fields
                    content.push(<tr key="h-vira" style={{ background: 'rgba(0,0,0,0.1)' }}><td colSpan="3" style={{ fontSize: '0.8rem', padding: '5px 25px', opacity: 0.5 }}>GERAL</td></tr>);

                    Object.entries(medicoes).forEach(([campo, valor]) => {
                        if (typeof valor !== 'object' && fields[campo]) {
                            const formatted = formatNumber(valor);
                            if (formatted) {
                                const refVal = refData?.[campo];
                                const inRangeResult = isInRange(valor, refVal, getValidationType(campo, 'virabrequim'));
                                content.push(
                                    <tr key={campo}>
                                        <td style={{ color: 'rgba(255,255,255,0.7)' }}>{fields[campo]}</td>
                                        <td style={{ fontStyle: 'italic', opacity: 0.5 }}>{refVal ? `${formatNumber(refVal)} mm` : '-'}</td>
                                        <td className={refVal ? (inRangeResult ? 'in-range' : 'out-range') : ''} style={{ fontWeight: 'bold', textAlign: 'center' }}>{formatted} mm</td>
                                    </tr>
                                );
                                hasData = true;
                            }
                        }
                    });

                    // Dynamic arrays (Moentes, Munhoes, etc)
                    const processArray = (key, label, refKey) => {
                        if (medicoes[key] && typeof medicoes[key] === 'object') {
                            content.push(<tr key={`h-${key}`} style={{ background: 'rgba(0,0,0,0.1)' }}><td colSpan="3" style={{ fontSize: '0.8rem', padding: '5px 25px', opacity: 0.5 }}>{label.toUpperCase()}</td></tr>);
                            const refVal = refData?.[refKey || key];
                            Object.entries(medicoes[key]).forEach(([i, v]) => {
                                const formatted = formatNumber(v);
                                if (formatted) {
                                    const inRangeResult = isInRange(v, refVal, 'exact');
                                    content.push(
                                        <tr key={`${key}-${i}`}>
                                            <td style={{ color: 'rgba(255,255,255,0.7)' }}>{label} {i}</td>
                                            <td style={{ fontStyle: 'italic', opacity: 0.5 }}>{refVal ? `${formatNumber(refVal)} mm` : '-'}</td>
                                            <td className={refVal ? (inRangeResult ? 'in-range' : 'out-range') : ''} style={{ fontWeight: 'bold', textAlign: 'center' }}>{formatted} mm</td>
                                        </tr>
                                    );
                                    hasData = true;
                                }
                            });
                        }
                    };

                    processArray('diametro_moente', 'Diâmetro Moente');
                    processArray('diametro_munhao', 'Diâmetro Munhão');
                    processArray('folga_mancal_ind', 'Folga Mancal', 'folga_mancal');
                    processArray('folga_biela_ind', tipo === 'rolamento' ? 'Folga Biela' : 'Folga Bronzina', 'folga_biela');
                }
                break;
            }

            case 'cabecote': {
                if (medicoes) {
                    // Organize by Cylinder
                    const cylinders = {};
                    const labels = {
                        adm_folga: 'Folga Adm',
                        esc_folga: 'Folga Esc',
                        adm_pastilha: 'Pastilha Adm',
                        esc_pastilha: 'Pastilha Esc'
                    };

                    Object.entries(medicoes).forEach(([type, data]) => {
                        if (data && typeof data === 'object') {
                            Object.entries(data).forEach(([side, values]) => {
                                if (values && typeof values === 'object') {
                                    Object.entries(values).forEach(([cyl, val]) => {
                                        if (!cylinders[cyl]) cylinders[cyl] = {};
                                        if (!cylinders[cyl][type]) cylinders[cyl][type] = {};
                                        cylinders[cyl][type][side] = val;
                                    });
                                }
                            });
                        }
                    });

                    Object.keys(cylinders).sort().forEach(cyl => {
                        content.push(
                            <tr key={`cyl-head-${cyl}`} style={{ background: 'rgba(228, 76, 101, 0.05)' }}>
                                <td colSpan="3" style={{ textAlign: 'center', fontWeight: 'bold', color: '#e44c65', letterSpacing: '2px', padding: '10px' }}>CILINDRO {cyl}</td>
                            </tr>
                        );

                        ['adm', 'esc'].forEach(vType => {
                            const folgaKey = `${vType}_folga`;
                            const pastilhaKey = `${vType}_pastilha`;
                            const refMin = refData?.[`val_${vType}_limite_min`];
                            const refMax = refData?.[`val_${vType}_limite_max`];
                            const refLabel = (refMin || refMax) ? `${formatNumber(refMin) || '0'}-${formatNumber(refMax) || '∞'} mm` : '-';

                            const sides = cylinders[cyl][folgaKey] ? Object.keys(cylinders[cyl][folgaKey]) : (cylinders[cyl][pastilhaKey] ? Object.keys(cylinders[cyl][pastilhaKey]) : []);

                            sides.sort().forEach(side => {
                                const folgaVal = cylinders[cyl][folgaKey]?.[side];
                                const pastVal = cylinders[cyl][pastilhaKey]?.[side];

                                if (folgaVal) {
                                    const inRangeResult = (!refMin || folgaVal >= refMin) && (!refMax || folgaVal <= refMax);
                                    content.push(
                                        <tr key={`${cyl}-${vType}-f-${side}`}>
                                            <td style={{ color: 'rgba(255,255,255,0.7)' }}>Folga Válv. {vType.toUpperCase()} ({side})</td>
                                            <td style={{ fontStyle: 'italic', opacity: 0.5 }}>{refLabel}</td>
                                            <td className={(refMin || refMax) ? (inRangeResult ? 'in-range' : 'out-range') : ''} style={{ fontWeight: 'bold', textAlign: 'center' }}>{formatNumber(folgaVal)} mm</td>
                                        </tr>
                                    );
                                    hasData = true;
                                }
                                if (pastVal) {
                                    content.push(
                                        <tr key={`${cyl}-${vType}-p-${side}`}>
                                            <td style={{ color: 'rgba(255,255,255,0.7)' }}>Pastilha Válv. {vType.toUpperCase()} ({side})</td>
                                            <td style={{ fontStyle: 'italic', opacity: 0.5 }}>-</td>
                                            <td style={{ textAlign: 'center', opacity: 0.8 }}>{formatNumber(pastVal)} mm</td>
                                        </tr>
                                    );
                                    hasData = true;
                                }
                            });
                        });
                    });
                }
                break;
            }

            default:
                break;
        }

        if (!hasData) return null;

        const isExpanded = expanded[tableKey];

        return (
            <div key={tableKey} className="component-section" style={{ marginBottom: '30px', boxShadow: '0 4px 15px rgba(0,0,0,0.2)' }}>
                <div className="component-header"
                    onClick={() => toggle(tableKey)}
                    style={{
                        padding: '18px 25px',
                        background: 'linear-gradient(90deg, #2d303a 0%, #24262e 100%)',
                        borderLeft: isExpanded ? '4px solid #e44c65' : '4px solid transparent',
                        transition: 'all 0.3s',
                        cursor: 'pointer',
                        display: 'flex',
                        justifyContent: 'space-between',
                        alignItems: 'center'
                    }}>
                    <h4 style={{ margin: 0, fontSize: '1.2rem', display: 'flex', alignItems: 'center', gap: '15px' }}>
                        <span style={{ fontSize: '1.5rem' }}>{icon}</span>
                        {title}
                    </h4>
                    <span style={{ opacity: 0.5, fontSize: '0.8rem' }}>{isExpanded ? 'RECOLHER ▲' : 'EXPANDIR ▼'}</span>
                </div>
                {isExpanded && (
                    <div style={{ animation: 'fadeIn 0.3s ease-in-out' }}>
                        <table className="measurements-table">
                            <thead>
                                <tr style={{ background: 'rgba(0,0,0,0.2)', fontSize: '0.8rem', fontWeight: 'bold', color: '#888' }}>
                                    <td style={{ padding: '10px 25px' }}>DESCRIÇÃO DA MEDIÇÃO</td>
                                    <td style={{ padding: '10px 25px' }}>REFERÊNCIA DE FÁBRICA</td>
                                    <td style={{ padding: '10px 25px', textAlign: 'center' }}>VALOR MEDIDO</td>
                                </tr>
                            </thead>
                            <tbody>
                                {content}
                            </tbody>
                        </table>
                    </div>
                )}
            </div>
        );
    };

    return (
        <div className="report-container" style={{ maxWidth: '1000px', background: 'transparent', padding: 0 }}>
            <div style={{
                display: 'flex',
                justifyContent: 'space-between',
                alignItems: 'center',
                marginBottom: '30px',
                borderBottom: '2px solid rgba(255,255,255,0.05)',
                paddingBottom: '20px'
            }}>
                <h2 style={{ fontSize: '2rem', display: 'flex', alignItems: 'center', gap: '15px', margin: 0 }}>
                    <span style={{ color: '#e44c65' }}>📊</span>
                    Relatório de Medições Técnicas - OS: {id}
                </h2>
                <div style={{ display: 'flex', gap: '10px' }}>
                    <button className="button secondary" style={{ padding: '8px 15px', fontSize: '0.8rem' }} onClick={expandAll}>EXPANDIR</button>
                    <button className="button secondary" style={{ padding: '8px 15px', fontSize: '0.8rem' }} onClick={collapseAll}>RECOLHER</button>
                    <button className="button primary" style={{ padding: '8px 15px', fontSize: '0.8rem', background: '#e44c65' }} onClick={handlePrint}>IMPRIMIR</button>
                </div>
            </div>
            <div style={{ display: 'flex', flexDirection: 'column', gap: '10px' }}>
                {components.map(c => renderComponentTable(c.key, c.title, c.icon))}
            </div>
        </div>
    );
}
