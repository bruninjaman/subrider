'use client';

import { useState } from 'react';
import { useRouter } from 'next/navigation';
import Link from 'next/link';

export default function AddServico() {
    const router = useRouter();
    const [formData, setFormData] = useState({
        item: '',
        tipo: ''
    });
    const [loading, setLoading] = useState(false);

    const handleSubmit = async (e) => {
        e.preventDefault();
        setLoading(true);

        try {
            const res = await fetch('/api/servicos', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(formData)
            });

            if (res.ok) {
                router.push('/tabelaServicos');
            } else {
                alert('Erro ao adicionar serviço');
            }
        } catch (error) {
            console.error(error);
            alert('Erro ao adicionar serviço');
        } finally {
            setLoading(false);
        }
    };

    return (
        <div className="container" style={{ padding: '60px 0', maxWidth: '800px', margin: '0 auto' }}>
            <div style={{
                marginBottom: '40px',
                borderLeft: '5px solid #e44c65',
                paddingLeft: '20px'
            }}>
                <h1 style={{ margin: 0, fontSize: '2.5rem' }}>Adicionar Serviço</h1>
                <p style={{ margin: '10px 0 0 0', opacity: 0.7 }}>Preencha os dados abaixo para adicionar um novo serviço.</p>
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
                        disabled={loading}
                        style={{
                            boxShadow: '0 4px 15px rgba(228, 76, 101, 0.3)',
                            opacity: loading ? 0.7 : 1,
                            cursor: loading ? 'wait' : 'pointer'
                        }}
                    >
                        {loading ? 'Adicionando...' : 'Adicionar Serviço'}
                    </button>
                </div>
            </form>
        </div>
    );
}
