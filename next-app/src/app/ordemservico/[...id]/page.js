'use client';

import { useState, useEffect } from 'react';
import { useParams } from 'next/navigation';
import { formatDate } from '@/lib/utils';
import OrderHeader from '@/components/OrderHeader';
import ItemsTable from '@/components/ItemsTable';
import MeasurementsReport from '@/components/MeasurementsReport';
import MotorcycleInfo from '@/components/MotorcycleInfo';
import MedicoesModal from '@/components/MedicoesModal';

export default function OrderPage() {
    const params = useParams();
    const id = Array.isArray(params.id) ? params.id.join('/') : params.id;
    const [data, setData] = useState(null);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState(null);
    const [isModalOpen, setIsModalOpen] = useState(false);

    useEffect(() => {
        async function fetchData() {
            try {
                const response = await fetch(`/api/ordemservico/${id}`);
                if (!response.ok) throw new Error('Falha ao carregar dados');
                const json = await response.json();
                setData(json);
            } catch (err) {
                setError(err.message);
            } finally {
                setLoading(false);
            }
        }
        if (id) fetchData();
    }, [id]);

    if (loading) return <div className="container" style={{ padding: '100px', textAlign: 'center', fontSize: '1.5rem', opacity: 0.5 }}>Carregando dados da ordem...</div>;
    if (error) return <div className="container" style={{ padding: '100px', textAlign: 'center', color: '#e44c65', fontSize: '1.2rem' }}>{error}</div>;
    if (!data) return <div className="container" style={{ padding: '100px', textAlign: 'center' }}>Ordem não encontrada.</div>;

    const { order, motorcycle, items, measurements, references } = data;

    const handlePrint = () => {
        window.print();
    };

    const handleAddData = () => {
        setIsModalOpen(true);
    };

    return (
        <div id="page-wrapper" className="landing" style={{ paddingBottom: '100px' }}>
            <div className="container">
                {/* Print Header - Visible only on print */}
                <div className="print-header" style={{ display: 'none', marginBottom: '30px', fontFamily: 'Arial, sans-serif' }}>
                    <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', marginBottom: '20px', borderBottom: '2px solid #000', paddingBottom: '20px' }}>
                        <div style={{ display: 'flex', alignItems: 'center', gap: '20px' }}>
                            {/* Try to use a black version of logo if available, or just filter it. Using filter: invert(1) might turn white to black if the image is transparent white. */}
                            <img src="/assets/css/images/logo-branco-crop.png" alt="Subrider" style={{ height: '60px', filter: 'invert(1) brightness(0)' }} />
                            <div>
                                <h1 style={{ margin: 0, fontSize: '24pt', fontWeight: 'bold', textTransform: 'uppercase', letterSpacing: '2px' }}>SUBRIDER</h1>
                                <p style={{ margin: 0, fontSize: '10pt', textTransform: 'uppercase' }}>Oficina Especializada</p>
                            </div>
                        </div>
                        <div style={{ textAlign: 'right', fontSize: '10pt' }}>
                            <p style={{ margin: 0 }}><strong>Localização:</strong> Quadra 12, conjunto L, St. Sul</p>
                            <p style={{ margin: 0 }}>Brasília - DF, 72415-612</p>
                            <p style={{ margin: 0, marginTop: '5px' }}><strong>WhatsApp:</strong> (61) 98128-2136</p>
                        </div>
                    </div>

                    <div style={{ display: 'flex', justifyContent: 'space-between', marginBottom: '30px', background: '#f5f5f5', padding: '15px', borderRadius: '8px', border: '1px solid #ddd' }}>
                        <div style={{ flex: 1 }}>
                            <h2 style={{ fontSize: '14pt', margin: '0 0 10px 0', borderBottom: '1px solid #ddd', paddingBottom: '5px' }}>DADOS DA ORDEM</h2>
                            <p style={{ margin: '5px 0' }}><strong>NÚMERO:</strong> #{id}</p>
                            <p style={{ margin: '5px 0' }}><strong>DATA:</strong> {formatDate(order.Data)}</p>
                        </div>
                        <div style={{ flex: 1, paddingLeft: '20px', borderLeft: '1px solid #ddd' }}>
                            <h2 style={{ fontSize: '14pt', margin: '0 0 10px 0', borderBottom: '1px solid #ddd', paddingBottom: '5px' }}>CLIENTE / VEÍCULO</h2>
                            <p style={{ margin: '5px 0' }}><strong>PROPRIETÁRIO:</strong> {order.proprietario_ordem}</p>
                            <p style={{ margin: '5px 0' }}><strong>VEÍCULO:</strong> {motorcycle ? `${motorcycle.marca} ${motorcycle.modelo}` : '---'}</p>
                            <p style={{ margin: '5px 0' }}><strong>PLACA:</strong> {motorcycle ? motorcycle.placa : '---'} <span style={{ marginLeft: '15px' }}><strong>ANO:</strong> {motorcycle ? motorcycle.ano : '---'}</span> <span style={{ marginLeft: '15px' }}><strong>KM:</strong> {order.KM}</span></p>
                        </div>
                    </div>
                </div>

                <div className="no-print">
                    <OrderHeader id={id} date={order.Data} owner={order.proprietario_ordem} km={order.KM} />
                </div>

                <div className="no-print" style={{
                    display: 'flex',
                    gap: '20px',
                    marginBottom: '50px',
                    flexWrap: 'wrap',
                    justifyContent: 'center',
                    background: 'rgba(255,255,255,0.02)',
                    padding: '25px',
                    borderRadius: '15px',
                    border: '1px solid rgba(255,255,255,0.05)',
                    backdropFilter: 'blur(5px)'
                }}>
                    <button className="button secondary" onClick={() => window.location.href = '/'} style={{ minWidth: '180px' }}>
                        <span style={{ marginRight: '8px' }}>⬅️</span> Voltar
                    </button>
                    <button className="button secondary" onClick={() => window.location.href = `/ordem_add_item?ordem=${id}`} style={{ minWidth: '180px' }}>
                        <span style={{ marginRight: '8px' }}>➕</span> Adicionar Item
                    </button>
                    <button className="button secondary" onClick={handleAddData} style={{ minWidth: '180px' }}>
                        <span style={{ marginRight: '8px' }}>📏</span> Medições
                    </button>
                    <button className="button secondary" onClick={() => window.location.href = `/relatorio?ordem=${id}`} style={{ minWidth: '180px' }}>
                        <span style={{ marginRight: '8px' }}>📝</span> Relatório
                    </button>
                    <button className="button primary" onClick={handlePrint} style={{ minWidth: '180px', background: 'linear-gradient(135deg, #c73650 0%, #e44c65 100%)' }}>
                        <span style={{ marginRight: '8px' }}>🖨️</span> Imprimir / PDF
                    </button>
                </div>


                <ItemsTable items={items} />

                <div className="no-print" style={{ marginTop: '50px' }}>
                    <MeasurementsReport id={id} measurements={measurements} references={references} />
                </div>

                {motorcycle && (
                    <div className="no-print" style={{ marginTop: '50px' }}>
                        <MotorcycleInfo motorcycle={motorcycle} />
                    </div>
                )}

                <footer className="no-print" style={{ textAlign: 'center', padding: '60px 0', borderTop: '1px solid rgba(255,255,255,0.05)', marginTop: '80px' }}>
                    <img src="/assets/css/images/logo-branco-crop.png" alt="Logo" style={{ height: '35px', opacity: 0.3, marginBottom: '20px' }} />
                    <p style={{ opacity: 0.3, fontSize: '0.9rem' }}>&copy; 2024 Subrider. Todos os direitos reservados.</p>
                </footer>
            </div>

            <MedicoesModal
                isOpen={isModalOpen}
                onClose={() => setIsModalOpen(false)}
                id={id}
                measurements={measurements}
                references={references}
            />
        </div>
    );
}
