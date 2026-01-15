'use client';

import { useState, useEffect, Suspense } from 'react';
import { useRouter, useSearchParams } from 'next/navigation';
import Link from 'next/link';

function EditServicoContent() {
    const router = useRouter();
    const searchParams = useSearchParams();
    const servicoId = searchParams.get('servicoID');

    const [formData, setFormData] = useState({
        item: '',
        tipo: ''
    });
    const [loading, setLoading] = useState(true);
    const [saving, setSaving] = useState(false);

    useEffect(() => {
        const fetchServico = async () => {
            if (!servicoId) return;
            try {
                const res = await fetch(`/api/servicos?id=${servicoId}`);
                const data = await res.json();
                if (data) {
                    setFormData({
                        item: data.item,
                        tipo: data.tipo
                    });
                }
            } catch (error) {
                console.error(error);
                alert('Erro ao carregar serviço');
            } finally {
                setLoading(false);
            }
        };

        fetchServico();
    }, [servicoId]);

    const handleSubmit = async (e) => {
        e.preventDefault();
        setSaving(true);

        try {
            const res = await fetch('/api/servicos', {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    servicoId,
                    ...formData
                })
            });

            if (res.ok) {
                router.push('/tabelaServicos');
            } else {
                alert('Erro ao atualizar serviço');
            }
        } catch (error) {
            console.error(error);
            alert('Erro ao atualizar serviço');
        } finally {
            setSaving(false);
        }
    };

    if (loading) {
        return (
            <div className="container" style={{ padding: '60px 0', textAlign: 'center' }}>
                <h2 style={{ opacity: 0.7 }}>Carregando dados...</h2>
            </div>
        );
    }

    return (
        <div className="container" style={{ padding: '60px 0', maxWidth: '800px', margin: '0 auto' }}>
            <div style={{
                marginBottom: '40px',
                borderLeft: '5px solid #e44c65',
                paddingLeft: '20px'
            }}>
                <h1 style={{ margin: 0, fontSize: '2.5rem' }}>Editar Serviço</h1>
                <p style={{ margin: '10px 0 0 0', opacity: 0.7 }}>Edite os dados do serviço abaixo.</p>
            </div>

            <form onSubmit={handleSubmit} style={{
                background: 'rgba(255,255,255,0.03)',
                padding: '40px',
                borderRadius: '15px',
                backdropFilter: 'blur(5px)',
                border: '1px solid rgba(255,255,255,0.05)'
            }}>
                <div style={{ display: 'flex', gap: '30px', marginBottom: '30px' }}>
                    <div style={{ flex: 1 }}>
                        <label style={{ display: 'block', marginBottom: '10px', fontWeight: 'bold' }}>Tipo</label>
                        <input
                            type="text"
                            required
                            value={formData.tipo}
                            onChange={(e) => setFormData({ ...formData, tipo: e.target.value })}
                            style={{
                                width: '100%',
                                padding: '15px',
                                borderRadius: '8px',
                                border: '1px solid rgba(255,255,255,0.1)',
                                background: 'rgba(0,0,0,0.2)',
                                color: 'white',
                                fontSize: '1rem'
                            }}
                        />
                    </div>
                    <div style={{ flex: 1 }}>
                        <label style={{ display: 'block', marginBottom: '10px', fontWeight: 'bold' }}>Item</label>
                        <input
                            type="text"
                            required
                            value={formData.item}
                            onChange={(e) => setFormData({ ...formData, item: e.target.value })}
                            style={{
                                width: '100%',
                                padding: '15px',
                                borderRadius: '8px',
                                border: '1px solid rgba(255,255,255,0.1)',
                                background: 'rgba(0,0,0,0.2)',
                                color: 'white',
                                fontSize: '1rem'
                            }}
                        />
                    </div>
                </div>

                <div style={{ display: 'flex', gap: '20px', justifyContent: 'flex-end', marginTop: '40px' }}>
                    <Link href="/tabelaServicos" className="button" style={{
                        background: 'transparent',
                        border: '1px solid rgba(255,255,255,0.2)'
                    }}>
                        Cancelar
                    </Link>
                    <button
                        type="submit"
                        className="button"
                        disabled={saving}
                        style={{
                            boxShadow: '0 4px 15px rgba(228, 76, 101, 0.3)',
                            opacity: saving ? 0.7 : 1,
                            cursor: saving ? 'wait' : 'pointer'
                        }}
                    >
                        {saving ? 'Salvando...' : 'Salvar Alterações'}
                    </button>
                </div>
            </form>
        </div>
    );
}

export default function EditServico() {
    return (
        <Suspense fallback={
            <div className="container" style={{ padding: '60px 0', textAlign: 'center' }}>
                <h2 style={{ opacity: 0.7 }}>Carregando...</h2>
            </div>
        }>
            <EditServicoContent />
        </Suspense>
    );
}
