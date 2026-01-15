'use client';

import { useState } from 'react';
import { formatNumber, isInRange, getValidationType } from '@/lib/utils';

export default function MeasurementsReport({ measurements, references, id }) {
    const [expanded, setExpanded] = useState({
        cabecote: true,
        motor: true,
        virabrequim: true,
        embreagem: true,
        bomba: true
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

        // Logic based on tabela-dados.php switch
        switch (tableKey) {
            case 'bomba': {
                if (row.medicoes) {
                    try {
                        const medicoes = JSON.parse(row.medicoes);
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
                                const inRange = isInRange(valor, refVal, getValidationType(campo, 'bomba'));
                                content.push(
                                    <tr key={campo}>
                                        <td style={{ color: 'rgba(255,255,255,0.7)' }}>{label}</td>
                                        <td className="reference-value" style={{ fontStyle: 'italic', opacity: 0.5 }}>{refVal ? `${formatNumber(refVal)} mm` : '-'}</td>
                                        <td className={refVal ? (inRange ? 'in-range' : 'out-range') : ''} style={{ fontWeight: 'bold', textAlign: 'center' }}>
                                            {formatted} {refVal ? 'mm' : ''}
                                        </td>
                                    </tr>
                                );
                                hasData = true;
                            }
                        });
                    } catch (e) { }
                }
                break;
            }

            case 'motor': {
                if (row.medicoes) {
                    try {
                        const medicoes = JSON.parse(row.medicoes);
                        const fields = {
                            curso_pistao: 'Curso Pistão',
                            diametro_cilindro_max: 'Diâm. Cilindro Máx',
                            conicidade_max: 'Conicidade Máx',
                            ovalizacao_max: 'Ovalização Máx',
                            diametro_pistao_min: 'Diâm. Pistão Mín',
                            folga_cil_pis_max: 'Folga Cil/Pis Máx'
                        };
                        Object.entries(medicoes).forEach(([cilindro, dados]) => {
                            if (typeof dados === 'object') {
                                content.push(
                                    <tr key={`cyl-${cilindro}`} style={{ background: 'rgba(228, 76, 101, 0.05)' }}>
                                        <td colSpan="3" style={{ textAlign: 'center', fontWeight: 'bold', color: '#e44c65', letterSpacing: '2px' }}>CILINDRO {cilindro}</td>
                                    </tr>
                                );
                                Object.entries(dados).forEach(([campo, valor]) => {
                                    const formatted = formatNumber(valor);
                                    if (formatted) {
                                        const refVal = refData?.[campo];
                                        const inRange = isInRange(valor, refVal, getValidationType(campo, 'motor'));
                                        content.push(
                                            <tr key={`${cilindro}-${campo}`}>
                                                <td style={{ color: 'rgba(255,255,255,0.7)' }}>{fields[campo] || campo}</td>
                                                <td className="reference-value" style={{ fontStyle: 'italic', opacity: 0.5 }}>{refVal ? `${formatNumber(refVal)} mm` : '-'}</td>
                                                <td className={refVal ? (inRange ? 'in-range' : 'out-range') : ''} style={{ fontWeight: 'bold', textAlign: 'center' }}>
                                                    {formatted} mm
                                                </td>
                                            </tr>
                                        );
                                        hasData = true;
                                    }
                                });
                            }
                        });
                    } catch (e) { }
                }
                break;
            }

            default:
                if (row.medicoes) {
                    try {
                        const medicoes = JSON.parse(row.medicoes);
                        Object.entries(medicoes).forEach(([campo, valor]) => {
                            if (typeof valor !== 'object') {
                                const formatted = formatNumber(valor);
                                if (formatted) {
                                    content.push(
                                        <tr key={campo}>
                                            <td style={{ color: 'rgba(255,255,255,0.7)' }}>{campo.replace(/_/g, ' ').toUpperCase()}</td>
                                            <td className="reference-value" style={{ opacity: 0.5 }}>-</td>
                                            <td style={{ fontWeight: 'bold', textAlign: 'center' }}>{formatted}</td>
                                        </tr>
                                    );
                                    hasData = true;
                                }
                            }
                        });
                    } catch (e) { }
                }
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
