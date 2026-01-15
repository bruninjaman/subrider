'use client';

import { useState, useEffect } from 'react';
import Link from 'next/link';

export default function TabelaPecas() {
    const [pecas, setPecas] = useState([]);
    const [search, setSearch] = useState('');
    const [loading, setLoading] = useState(true);
    const [orderby, setOrderby] = useState('pecaId');
    const [orderDir, setOrderDir] = useState('DESC');
    const [page, setPage] = useState(1);
    const [totalPages, setTotalPages] = useState(0);

    const fetchPecas = async () => {
        setLoading(true);
        try {
            const res = await fetch(`/api/pecas?q=${encodeURIComponent(search)}&orderby=${orderby}&order=${orderDir}&page=${page}&limit=5`, { cache: 'no-store' });
            const data = await res.json();
            if (data.items) {
                setPecas(data.items);
                setTotalPages(data.totalPages);
            } else {
                setPecas([]);
            }
        } catch (err) {
            console.error(err);
        } finally {
            setLoading(false);
        }
    };

    useEffect(() => {
        fetchPecas();
    }, [orderby, orderDir, page]);

    const handleSearch = (e) => {
        if (e.key === 'Enter') {
            setPage(1); // Reset to page 1 on search
            fetchPecas();
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

    const getImageUrl = (path) => {
        if (!path) return '';
        // Remove ../ if present and ensure it starts with /
        return '/' + path.replace('../', '').replace(/^\/+/, '');
    };

    const [showDeleteModal, setShowDeleteModal] = useState(false);
    const [pecaToDelete, setPecaToDelete] = useState(null);

    const handleDeleteClick = (peca) => {
        setPecaToDelete(peca);
        setShowDeleteModal(true);
    };

    const confirmDelete = async () => {
        if (!pecaToDelete) return;

        const { pecaId } = pecaToDelete;
        setShowDeleteModal(false);

        try {
            console.log('Sending DELETE request...');
            const res = await fetch(`/api/pecas?id=${pecaId}`, {
                method: 'DELETE',
            });

            if (res.ok) {
                console.log('Delete successful');
                fetchPecas();
            } else {
                const data = await res.json();
                console.error('Delete failed:', data);
                alert(data.error || 'Erro ao excluir peça');
            }
        } catch (err) {
            console.error('Delete error:', err);
            alert('Erro ao excluir peça');
        } finally {
            setPecaToDelete(null);
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
                <h1 style={{ margin: 0, fontSize: '2.5rem' }}>Tabela de Peças</h1>
                <Link href="/tabelaPecasAdd" className="button" style={{ boxShadow: '0 4px 15px rgba(228, 76, 101, 0.3)', display: 'flex', alignItems: 'center', gap: '10px' }}>
                    <img src="/assets/css/images/addpeca.png" style={{ width: '30px', height: '30px' }} alt="" />
                    Adicionar Item
                </Link>
            </div>

            <div className="search-container" style={{ background: 'rgba(255,255,255,0.03)', padding: '20px', borderRadius: '15px', backdropFilter: 'blur(5px)' }}>
                <input
                    type="text"
                    className="search-input"
                    placeholder="Pesquisar por item, grupo ou parte..."
                    value={search}
                    onChange={(e) => setSearch(e.target.value)}
                    onKeyDown={handleSearch}
                    style={{ fontSize: '1.1rem' }}
                />
                <button className="button" onClick={() => { setPage(1); fetchPecas(); }} style={{ padding: '0 30px' }}>Pesquisar</button>
            </div>

            <div className="table-wrapper" style={{ marginTop: '40px', background: 'transparent', padding: 0 }}>
                <table className="measurements-table" style={{ background: 'rgba(255,255,255,0.02)', borderRadius: '15px', overflow: 'hidden' }}>
                    <thead>
                        <tr className="section-header" style={{ background: 'rgba(228, 76, 101, 0.1)' }}>
                            <td style={{ padding: '20px', fontWeight: 'bold' }}>Foto</td>
                            <td onClick={() => toggleSort('grupo')} style={{ cursor: 'pointer', padding: '20px', fontWeight: 'bold' }}>
                                Grupo {orderby === 'grupo' ? (orderDir === 'ASC' ? '↑' : '↓') : ''}
                            </td>
                            <td onClick={() => toggleSort('item')} style={{ cursor: 'pointer', padding: '20px', fontWeight: 'bold' }}>
                                Item {orderby === 'item' ? (orderDir === 'ASC' ? '↑' : '↓') : ''}
                            </td>
                            <td onClick={() => toggleSort('parte')} style={{ cursor: 'pointer', padding: '20px', fontWeight: 'bold' }}>
                                Parte {orderby === 'parte' ? (orderDir === 'ASC' ? '↑' : '↓') : ''}
                            </td>
                            <td style={{ textAlign: 'center', padding: '20px', fontWeight: 'bold' }}>Ações</td>
                        </tr>
                    </thead>
                    <tbody>
                        {loading ? (
                            <tr>
                                <td colSpan="5" style={{ textAlign: 'center', padding: '100px', fontSize: '1.2rem', opacity: 0.5 }}>
                                    Carregando dados do sistema...
                                </td>
                            </tr>
                        ) : pecas.length > 0 ? (
                            pecas.map(peca => (
                                <tr
                                    key={peca.pecaId}
                                    style={{ borderBottom: '1px solid rgba(255,255,255,0.05)' }}
                                    onMouseOver={(e) => e.currentTarget.style.background = 'rgba(255,255,255,0.02)'}
                                    onMouseOut={(e) => e.currentTarget.style.background = 'transparent'}
                                >
                                    <td style={{ padding: '15px' }}>
                                        {peca.foto && (
                                            <img
                                                src={getImageUrl(peca.foto)}
                                                alt={peca.item}
                                                style={{ width: '50px', height: '50px', objectFit: 'cover', borderRadius: '5px' }}
                                                onError={(e) => { e.target.style.display = 'none'; }}
                                            />
                                        )}
                                    </td>
                                    <td style={{ padding: '20px', fontSize: '1.05rem' }}>{peca.grupo}</td>
                                    <td style={{ padding: '20px', fontSize: '1.05rem' }}>{peca.item}</td>
                                    <td style={{ padding: '20px', fontSize: '1.05rem', opacity: 0.8 }}>{peca.parte}</td>
                                    <td>
                                        <div style={{ display: 'flex', gap: '20px', justifyContent: 'center', alignItems: 'center' }}>
                                            <Link
                                                href={`/tabelaPecasEdit?pecaID=${peca.pecaId}`}
                                                style={{ background: 'none', border: 'none', cursor: 'pointer', padding: 0, transition: 'transform 0.2s', display: 'inline-block' }}
                                            >
                                                <div
                                                    onMouseOver={(e) => e.currentTarget.style.transform = 'scale(1.2)'}
                                                    onMouseOut={(e) => e.currentTarget.style.transform = 'scale(1)'}
                                                    style={{ transition: 'transform 0.2s' }}
                                                >
                                                    <img
                                                        src="/assets/css/images/edit-peca.png"
                                                        style={{ height: '30px', width: '30px', filter: 'drop-shadow(0 2px 5px rgba(0,0,0,0.5))' }}
                                                        alt="Editar"
                                                    />
                                                </div>
                                            </Link>
                                            <button
                                                onClick={() => handleDeleteClick(peca)}
                                                style={{ background: 'none', border: 'none', cursor: 'pointer', padding: 0, transition: 'transform 0.2s' }}
                                                onMouseOver={(e) => e.currentTarget.style.transform = 'scale(1.2)'}
                                                onMouseOut={(e) => e.currentTarget.style.transform = 'scale(1)'}
                                                title="Excluir"
                                            >
                                                <img
                                                    src="/assets/css/images/x-button-peca.png"
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
                                <td colSpan="5" style={{ textAlign: 'center', padding: '100px', opacity: 0.5 }}>
                                    Nenhum item encontrado.
                                </td>
                            </tr>
                        )}
                    </tbody>
                </table>
            </div>

            {/* Pagination */}
            {totalPages > 1 && (
                <div style={{ display: 'flex', justifyContent: 'center', marginTop: '30px', gap: '10px' }}>
                    <button
                        onClick={() => setPage(p => Math.max(1, p - 1))}
                        disabled={page === 1}
                        className="button secondary"
                        style={{ opacity: page === 1 ? 0.5 : 1 }}
                    >
                        Anterior
                    </button>
                    <span style={{ display: 'flex', alignItems: 'center', padding: '0 15px', fontSize: '1.1rem' }}>
                        Página {page} de {totalPages}
                    </span>
                    <button
                        onClick={() => setPage(p => Math.min(totalPages, p + 1))}
                        disabled={page === totalPages}
                        className="button secondary"
                        style={{ opacity: page === totalPages ? 0.5 : 1 }}
                    >
                        Próxima
                    </button>
                </div>
            )}

            {/* Delete Confirmation Modal */}
            {showDeleteModal && pecaToDelete && (
                <div className="modal-overlay" onClick={() => setShowDeleteModal(false)}>
                    <div className="modal-content" onClick={(e) => e.stopPropagation()} style={{ maxWidth: '400px', padding: '30px', textAlign: 'center' }}>
                        <h2 style={{ color: '#e44c65', marginBottom: '15px' }}>Confirmar Exclusão</h2>
                        <p style={{ marginBottom: '30px', fontSize: '1.1rem' }}>
                            Deseja realmente excluir o item <br />
                            <strong style={{ color: 'white' }}>"{pecaToDelete.item}"</strong>?
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
