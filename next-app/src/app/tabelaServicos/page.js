'use client';

import { useState, useEffect } from 'react';
import Link from 'next/link';

export default function TabelaServicos() {
    const [servicos, setServicos] = useState([]);
    const [search, setSearch] = useState('');
    const [loading, setLoading] = useState(true);
    const [orderby, setOrderby] = useState('servicoId');
    const [orderDir, setOrderDir] = useState('DESC');

    const fetchServicos = async () => {
        setLoading(true);
        try {
            const res = await fetch(`/api/servicos?q=${encodeURIComponent(search)}&orderby=${orderby}&order=${orderDir}`, { cache: 'no-store' });
            const data = await res.json();
            if (Array.isArray(data)) {
                setServicos(data);
            }
        } catch (err) {
            console.error(err);
        } finally {
            setLoading(false);
        }
    };

    useEffect(() => {
        fetchServicos();
    }, [orderby, orderDir]);

    const handleSearch = (e) => {
        if (e.key === 'Enter') {
            fetchServicos();
        }
    };

    const toggleSort = (field) => {
        if (orderby === field) {
            setOrderDir(orderDir === 'ASC' ? 'DESC' : 'ASC');
        } else {
            setOrderby(field);
            setOrderDir('DESC');
        }
    };

    const [showDeleteModal, setShowDeleteModal] = useState(false);
    const [serviceToDelete, setServiceToDelete] = useState(null);

    const handleDeleteClick = (servico) => {
        setServiceToDelete(servico);
        setShowDeleteModal(true);
    };

    const confirmDelete = async () => {
        if (!serviceToDelete) return;

        const { servicoId, item } = serviceToDelete;
        setShowDeleteModal(false);

        try {
            console.log('Sending DELETE request...');
            const res = await fetch(`/api/servicos?id=${servicoId}`, {
                method: 'DELETE',
            });

            if (res.ok) {
                console.log('Delete successful');
                // Refresh the list
                fetchServicos();
            } else {
                const data = await res.json();
                console.error('Delete failed:', data);
                alert(data.error || 'Erro ao excluir serviço');
            }
        } catch (err) {
            console.error('Delete error:', err);
            alert('Erro ao excluir serviço');
        } finally {
            setServiceToDelete(null);
        }
    };

    return (
        <div className="container" style={{ padding: '60px 0' }}>
            <div style={{
                display: 'flex',
                justifyContent: 'space-between',
                alignItems: 'center',
                marginBottom: '50px',
                borderLeft: '5px solid #e44c65',
                paddingLeft: '20px'
            }}>
                <h1 style={{ margin: 0, fontSize: '2.5rem' }}>Tabela de Serviços</h1>
                <Link href="/tabelaServicosAdd" className="button" style={{ boxShadow: '0 4px 15px rgba(228, 76, 101, 0.3)' }}>
                    Adicionar Serviço
                </Link>
            </div>

            <div className="search-container" style={{ background: 'rgba(255,255,255,0.03)', padding: '20px', borderRadius: '15px', backdropFilter: 'blur(5px)' }}>
                <input
                    type="text"
                    className="search-input"
                    placeholder="Pesquisar por item ou tipo..."
                    value={search}
                    onChange={(e) => setSearch(e.target.value)}
                    onKeyDown={handleSearch}
                    style={{ fontSize: '1.1rem' }}
                />
                <button className="button" onClick={fetchServicos} style={{ padding: '0 30px' }}>Pesquisar</button>
            </div>

            <div className="table-wrapper" style={{ marginTop: '40px', background: 'transparent', padding: 0 }}>
                <table className="measurements-table" style={{ background: 'rgba(255,255,255,0.02)', borderRadius: '15px', overflow: 'hidden' }}>
                    <thead>
                        <tr className="section-header" style={{ background: 'rgba(228, 76, 101, 0.1)' }}>
                            <td onClick={() => toggleSort('item')} style={{ cursor: 'pointer', padding: '20px', fontWeight: 'bold' }}>
                                Item {orderby === 'item' ? (orderDir === 'ASC' ? '↑' : '↓') : ''}
                            </td>
                            <td onClick={() => toggleSort('tipo')} style={{ cursor: 'pointer', padding: '20px', fontWeight: 'bold' }}>
                                Tipo {orderby === 'tipo' ? (orderDir === 'ASC' ? '↑' : '↓') : ''}
                            </td>
                            <td style={{ textAlign: 'center', padding: '20px', fontWeight: 'bold' }}>Ações</td>
                        </tr>
                    </thead>
                    <tbody>
                        {loading ? (
                            <tr>
                                <td colSpan="3" style={{ textAlign: 'center', padding: '100px', fontSize: '1.2rem', opacity: 0.5 }}>
                                    Carregando dados do sistema...
                                </td>
                            </tr>
                        ) : servicos.length > 0 ? (
                            servicos.map(servico => (
                                <tr
                                    key={servico.servicoId}
                                    style={{ borderBottom: '1px solid rgba(255,255,255,0.05)' }}
                                    onMouseOver={(e) => e.currentTarget.style.background = 'rgba(255,255,255,0.02)'}
                                    onMouseOut={(e) => e.currentTarget.style.background = 'transparent'}
                                >
                                    <td style={{ padding: '20px', fontSize: '1.05rem' }}>{servico.item}</td>
                                    <td style={{ padding: '20px', fontSize: '1.05rem', opacity: 0.8 }}>{servico.tipo}</td>
                                    <td>
                                        <div style={{ display: 'flex', gap: '20px', justifyContent: 'center', alignItems: 'center' }}>
                                            <button
                                                onClick={() => window.location.href = `/tabelaServicosEdit?servicoID=${servico.servicoId}`}
                                                style={{ background: 'none', border: 'none', cursor: 'pointer', padding: 0, transition: 'transform 0.2s' }}
                                                onMouseOver={(e) => e.currentTarget.style.transform = 'scale(1.2)'}
                                                onMouseOut={(e) => e.currentTarget.style.transform = 'scale(1)'}
                                                title="Editar"
                                            >
                                                <img
                                                    src="/assets/css/images/edit.png"
                                                    style={{ height: '30px', width: '30px', filter: 'drop-shadow(0 2px 5px rgba(0,0,0,0.5))' }}
                                                    alt="Editar"
                                                />
                                            </button>
                                            <button
                                                onClick={() => handleDeleteClick(servico)}
                                                style={{ background: 'none', border: 'none', cursor: 'pointer', padding: 0, transition: 'transform 0.2s' }}
                                                onMouseOver={(e) => e.currentTarget.style.transform = 'scale(1.2)'}
                                                onMouseOut={(e) => e.currentTarget.style.transform = 'scale(1)'}
                                                title="Excluir"
                                            >
                                                <img
                                                    src="/assets/css/images/x-button.png"
                                                    style={{ height: '30px', width: '30px', filter: 'drop-shadow(0 2px 5px rgba(0,0,0,0.5))' }}
                                                    alt="Excluir"
                                                />
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            ))
                        ) : (
                            <tr>
                                <td colSpan="3" style={{ textAlign: 'center', padding: '100px', opacity: 0.5 }}>
                                    Nenhum serviço encontrado para os critérios de busca.
                                </td>
                            </tr>
                        )}
                    </tbody>
                </table>
            </div>

            {/* Delete Confirmation Modal */}
            {showDeleteModal && serviceToDelete && (
                <div className="modal-overlay" onClick={() => setShowDeleteModal(false)}>
                    <div className="modal-content" onClick={(e) => e.stopPropagation()} style={{ maxWidth: '400px', padding: '30px', textAlign: 'center' }}>
                        <h2 style={{ color: '#e44c65', marginBottom: '15px' }}>Confirmar Exclusão</h2>
                        <p style={{ marginBottom: '30px', fontSize: '1.1rem' }}>
                            Deseja realmente excluir o serviço <br />
                            <strong style={{ color: 'white' }}>"{serviceToDelete.item}"</strong>?
                        </p>
                        <div style={{ display: 'flex', justifyContent: 'center', gap: '15px' }}>
                            <button
                                className="button secondary"
                                onClick={() => setShowDeleteModal(false)}
                                style={{ flex: 1 }}
                            >
                                Cancelar
                            </button>
                            <button
                                className="button"
                                onClick={confirmDelete}
                                style={{ flex: 1, backgroundColor: '#c73650' }}
                            >
                                Excluir
                            </button>
                        </div>
                    </div>
                </div>
            )}
        </div>
    );
}
