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
        setExpanded({ cabecote: true, motor: true, virabrequim: true, embreagem: true, bomba: true });
    };

    const collapseAll = () => {
        setExpanded({ cabecote: false, motor: false, virabrequim: false, embreagem: false, bomba: false });
    };

    const handlePrint = () => {
        expandAll();
        setTimeout(() => { window.print(); }, 300);
    };

    const components = [
        { key: 'cabecote', title: 'Cabeçote', icon: '🔧' },
        { key: 'motor', title: 'Motor', icon: '⚙️' },
        { key: 'virabrequim', title: 'Virabrequim', icon: '🔩' },
        { key: 'embreagem', title: 'Embreagem', icon: '🔄' },
        { key: 'bomba', title: 'Bomba', icon: '⛽' }
    ];

    const parseMedicoes = (json) => {
        if (!json) return null;
        try { return typeof json === 'string' ? JSON.parse(json) : json; }
        catch (e) { return null; }
    };

    const renderComponentTable = (tableKey, title, icon) => {
        const row = measurements[tableKey];
        const refData = references[tableKey]?.medicoes ? parseMedicoes(references[tableKey].medicoes) : null;
        if (!row) return null;

        const medicoes = parseMedicoes(row.medicoes);
        if (!medicoes) return null;

        let content = [];
        let hasData = false;

        switch (tableKey) {
            case 'cabecote': {
                const cilCount = parseInt(refData?.num_cilindros) || 0;
                for (let c = 1; c <= cilCount; c++) {
                    content.push(<tr key={`h-c-${c}`} className="group-header"><td>CILINDRO {c}</td><td>-</td><td>-</td></tr>);

                    // Admissão
                    ['direita', 'esquerda'].forEach(side => {
                        const folga = medicoes[`adm_folga_${side}`]?.[c];
                        const refMin = refData?.val_adm_limite_min;
                        const refMax = refData?.val_adm_limite_max;
                        if (folga) {
                            const inRange = isInRange(folga, { min: refMin, max: refMax }, 'range');
                            content.push(<tr key={`af-${side}-${c}`}><td>Folga Adm ({side})</td><td>{refMin}-{refMax}</td><td className={inRange ? 'in-range' : 'out-range'}>{formatNumber(folga)} mm</td></tr>);
                            hasData = true;
                        }
                        const past = medicoes[`adm_pastilha_${side}`]?.[c];
                        if (past) {
                            content.push(<tr key={`ap-${side}-${c}`}><td>Pastilha Adm ({side})</td><td>-</td><td>{formatNumber(past)} mm</td></tr>);
                            hasData = true;
                        }
                    });

                    // Escape
                    ['direita', 'esquerda'].forEach(side => {
                        const folga = medicoes[`esc_folga_${side}`]?.[c];
                        const refMin = refData?.val_esc_limite_min;
                        const refMax = refData?.val_esc_limite_max;
                        if (folga) {
                            const inRange = isInRange(folga, { min: refMin, max: refMax }, 'range');
                            content.push(<tr key={`ef-${side}-${c}`}><td>Folga Esc ({side})</td><td>{refMin}-{refMax}</td><td className={inRange ? 'in-range' : 'out-range'}>{formatNumber(folga)} mm</td></tr>);
                            hasData = true;
                        }
                        const past = medicoes[`esc_pastilha_${side}`]?.[c];
                        if (past) {
                            content.push(<tr key={`ep-${side}-${c}`}><td>Pastilha Esc ({side})</td><td>-</td><td>{formatNumber(past)} mm</td></tr>);
                            hasData = true;
                        }
                    });

                    const comp = medicoes.compressao?.[c];
                    if (comp) {
                        content.push(<tr key={`comp-${c}`}><td>Compressão</td><td>{refData?.compressao_min}-{refData?.compressao_max}</td><td>{formatNumber(comp)}</td></tr>);
                        hasData = true;
                    }
                }
                break;
            }

            case 'motor': {
                const cilCount = parseInt(refData?.nr_cilindros) || 0;
                const fields = [
                    { id: 'diametro_cilindro', label: 'Diâm. Cilindro', ref: 'diametro_cilindro_max', type: 'max' },
                    { id: 'diametro_pistao', label: 'Diâm. Pistão', ref: 'diametro_pistao_min', type: 'min' },
                    { id: 'conicidade', label: 'Conicidade', ref: 'conicidade_max', type: 'max' },
                    { id: 'ovalizacao', label: 'Ovalização', ref: 'ovalizacao_max', type: 'max' }
                ];

                for (let c = 1; c <= cilCount; c++) {
                    content.push(<tr key={`h-m-${c}`} className="group-header"><td>CILINDRO {c}</td><td>-</td><td>-</td></tr>);
                    fields.forEach(f => {
                        const val = medicoes[f.id]?.[c];
                        if (val) {
                            const refVal = refData?.[f.ref];
                            const inRange = isInRange(val, refVal, f.type);
                            content.push(<tr key={`f-m-${f.id}-${c}`}><td>{f.label}</td><td>{refVal || '-'}</td><td className={inRange ? 'in-range' : 'out-range'}>{formatNumber(val)} mm</td></tr>);
                            hasData = true;
                        }
                    });
                }
                break;
            }

            case 'virabrequim': {
                const cilCount = parseInt(refData?.qtd_cilindros) || 0;
                const isBronzina = refData?.rolamento_type === 'bronzina';

                for (let c = 1; c <= cilCount; c++) {
                    content.push(<tr key={`h-v-${c}`} className="group-header"><td>CILINDRO {c}</td><td>-</td><td>-</td></tr>);
                    if (isBronzina) {
                        const moente = medicoes.diametro_moente?.[c];
                        if (moente) {
                            content.push(<tr key={`v-mo-${c}`}><td>Diâm. Moente</td><td>{refData?.diametro_moente}</td><td>{formatNumber(moente)} mm</td></tr>);
                            hasData = true;
                        }
                        const folgaB = medicoes.folga_biela?.[c];
                        if (folgaB) {
                            const inRange = isInRange(folgaB, refData?.folga_biela, 'max');
                            content.push(<tr key={`v-fb-${c}`}><td>Folga Bronzina</td><td>{refData?.folga_biela} MAX</td><td className={inRange ? 'in-range' : 'out-range'}>{formatNumber(folgaB)} mm</td></tr>);
                            hasData = true;
                        }
                    } else {
                        const folgaBi = medicoes.folga_biela?.[c];
                        if (folgaBi) {
                            const inRange = isInRange(folgaBi, refData?.folga_biela, 'max');
                            content.push(<tr key={`v-fi-${c}`}><td>Folga Biela</td><td>{refData?.folga_biela} MAX</td><td className={inRange ? 'in-range' : 'out-range'}>{formatNumber(folgaBi)} mm</td></tr>);
                            hasData = true;
                        }
                    }
                    const empen = medicoes.empenamento?.[c];
                    if (empen) {
                        const inRange = isInRange(empen, refData?.empenamento, 'max');
                        content.push(<tr key={`v-em-${c}`}><td>Empenamento</td><td>{refData?.empenamento} MAX</td><td className={inRange ? 'in-range' : 'out-range'}>{formatNumber(empen)} mm</td></tr>);
                        hasData = true;
                    }
                }
                break;
            }

            case 'embreagem': {
                const fric = medicoes.disco_fric_esp?.[0];
                const sep = medicoes.disco_sep_emp?.[0];
                if (fric) {
                    const inRange = isInRange(fric, refData?.disco_fric_esp_min, 'min');
                    content.push(<tr key="emb-f"><td>Espessura Fricção (Média)</td><td>{refData?.disco_fric_esp_min} MIN</td><td className={inRange ? 'in-range' : 'out-range'}>{formatNumber(fric)} mm</td></tr>);
                    hasData = true;
                }
                if (sep) {
                    const inRange = isInRange(sep, refData?.disco_sep_emp_max, 'max');
                    content.push(<tr key="emb-s"><td>Empenamento Separador (Máx)</td><td>{refData?.disco_sep_emp_max} MAX</td><td className={inRange ? 'in-range' : 'out-range'}>{formatNumber(sep)} mm</td></tr>);
                    hasData = true;
                }
                break;
            }

            case 'bomba': {
                const prOleo = medicoes.pressao_oleo?.[0];
                if (prOleo) {
                    const inRange = isInRange(prOleo, { min: refData?.pressao_oleo_min, max: refData?.pressao_oleo_max }, 'range');
                    content.push(<tr key="b-o"><td>Pressão Óleo</td><td>{refData?.pressao_oleo_min}-{refData?.pressao_oleo_max}</td><td className={inRange ? 'in-range' : 'out-range'}>{formatNumber(prOleo)}</td></tr>);
                    hasData = true;
                }
                const prComb = medicoes.pressao_combustao?.[0];
                if (prComb) {
                    content.push(<tr key="b-c"><td>Pressão Combustível</td><td>{refData?.pressao_combustao} REF</td><td>{formatNumber(prComb)}</td></tr>);
                    hasData = true;
                }
                const vaComb = medicoes.vazao_combustao?.[0];
                if (vaComb) {
                    const inRange = isInRange(vaComb, { min: refData?.vazao_combustao_min, max: refData?.vazao_combustao_max }, 'range');
                    content.push(<tr key="b-v"><td>Vazão Combustível</td><td>{refData?.vazao_combustao_min}-{refData?.vazao_combustao_max}</td><td className={inRange ? 'in-range' : 'out-range'}>{formatNumber(vaComb)}</td></tr>);
                    hasData = true;
                }
                break;
            }
        }

        if (!hasData) return null;

        const isExpanded = expanded[tableKey];

        return (
            <div key={tableKey} className="component-section" style={{ marginBottom: '30px', boxShadow: '0 4px 15px rgba(0,0,0,0.2)', borderRadius: '15px', overflow: 'hidden' }}>
                <div className="component-header" onClick={() => toggle(tableKey)}
                    style={{
                        padding: '18px 25px', background: 'linear-gradient(90deg, #2d303a 0%, #24262e 100%)',
                        borderLeft: isExpanded ? '4px solid #e44c65' : '4px solid transparent',
                        cursor: 'pointer', display: 'flex', justifyContent: 'space-between', alignItems: 'center'
                    }}>
                    <h4 style={{ margin: 0, fontSize: '1.2rem', display: 'flex', alignItems: 'center', gap: '15px' }}>
                        <span style={{ fontSize: '1.5rem' }}>{icon}</span> {title}
                    </h4>
                    <span style={{ opacity: 0.5, fontSize: '0.8rem' }}>{isExpanded ? 'RECOLHER ▲' : 'EXPANDIR ▼'}</span>
                </div>
                {isExpanded && (
                    <div style={{ animation: 'fadeIn 0.3s ease-in-out' }}>
                        <table className="measurements-table">
                            <thead>
                                <tr style={{ background: 'rgba(0,0,0,0.2)', fontSize: '0.8rem', fontWeight: 'bold', color: '#888' }}>
                                    <td style={{ padding: '10px 25px' }}>DESCRIÇÃO DA MEDIÇÃO</td>
                                    <td style={{ padding: '10px 25px' }}>REFERÊNCIA</td>
                                    <td style={{ padding: '10px 25px', textAlign: 'center' }}>VALOR MEDIDO</td>
                                </tr>
                            </thead>
                            <tbody>{content}</tbody>
                        </table>
                    </div>
                )}
            </div>
        );
    };

    return (
        <div className="report-container" style={{ maxWidth: '1000px', margin: '0 auto', padding: '20px' }}>
            <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', marginBottom: '30px', borderBottom: '2px solid rgba(255,255,255,0.05)', paddingBottom: '20px' }}>
                <h2 style={{ fontSize: '1.8rem', margin: 0 }}><span style={{ color: '#e44c65' }}>📊</span> Relatório Técnico - OS: {id}</h2>
                <div style={{ display: 'flex', gap: '10px' }}>
                    <button className="button secondary" onClick={expandAll}>EXPANDIR</button>
                    <button className="button secondary" onClick={collapseAll}>RECOLHER</button>
                    <button className="button primary" onClick={handlePrint}>IMPRIMIR</button>
                </div>
            </div>
            {components.map(c => renderComponentTable(c.key, c.title, c.icon))}
            <style jsx>{`
                .button { padding: 10px 20px; border-radius: 8px; font-weight: 600; cursor: pointer; border: none; transition: 0.2s; }
                .button.primary { background: #e44c65; color: white; }
                .button.secondary { background: rgba(255,255,255,0.05); color: white; border: 1px solid rgba(255,255,255,0.1); }
                .measurements-table { width: 100%; border-collapse: collapse; background: rgba(255,255,255,0.02); }
                .measurements-table td { padding: 12px 25px; border-bottom: 1px solid rgba(255,255,255,0.05); font-size: 0.95rem; }
                .in-range { color: #00c063; font-weight: bold; text-align: center; }
                .out-range { color: #e44c65; font-weight: bold; text-align: center; }
                .group-header td { background: rgba(228, 76, 101, 0.1); color: #e44c65; font-weight: bold; text-align: center; letter-spacing: 1px; }
            `}</style>
        </div>
    );
}
