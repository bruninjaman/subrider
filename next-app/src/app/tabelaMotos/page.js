
'use client';

import { useState, useEffect } from 'react';
import Link from 'next/link';

export default function TabelaMotos() {
    const [motos, setMotos] = useState([]);
    const [search, setSearch] = useState('');
    const [loading, setLoading] = useState(true);
    const [orderby, setOrderby] = useState('motoId');
    const [orderDir, setOrderDir] = useState('DESC');
    const [page, setPage] = useState(1);
    const [totalPages, setTotalPages] = useState(0);

    const fetchMotos = async () => {
        setLoading(true);
        try {
            const res = await fetch(`/api/motos?q=${encodeURIComponent(search)}&orderby=${orderby}&order=${orderDir}&page=${page}&limit=5`, { cache: 'no-store' });
            const data = await res.json();
            if (data.items) {
                setMotos(data.items);
                setTotalPages(data.totalPages);
            } else {
                setMotos([]);
            }
        } catch (err) {
            console.error(err);
        } finally {
            setLoading(false);
        }
    };

    useEffect(() => {
        fetchMotos();
    }, [orderby, orderDir, page]);

    const handleSearch = (e) => {
        if (e.key === 'Enter') {
            setPage(1); // Reset to page 1 on search
            fetchMotos();
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

    const formatKM = (km) => {
        if (!km) return '0';
        return km.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    };

    const [showDeleteModal, setShowDeleteModal] = useState(false);
    const [motoToDelete, setMotoToDelete] = useState(null);

    const handleDeleteClick = (moto) => {
        setMotoToDelete(moto);
        setShowDeleteModal(true);
    };

    const confirmDelete = async () => {
        if (!motoToDelete) return;

        const { motoId } = motoToDelete;
        setShowDeleteModal(false);

        try {
            console.log('Sending DELETE request...');
            const res = await fetch(`/api/motos?id=${motoId}`, {
                method: 'DELETE',
            });

            if (res.ok) {
                console.log('Delete successful');
                fetchMotos();
            } else {
                const data = await res.json();
                console.error('Delete failed:', data);
                alert(data.error || 'Erro ao excluir motocicleta');
            }
        } catch (err) {
            console.error('Delete error:', err);
            alert('Erro ao excluir motocicleta');
        } finally {
            setMotoToDelete(null);
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
                <h1 style={{ margin: 0, fontSize: '2.5rem' }}>Tabela de Motos</h1>
                <Link href="/addmotos" className="button" style={{ boxShadow: '0 4px 15px rgba(228, 76, 101, 0.3)', display: 'flex', alignItems: 'center', gap: '10px' }}>
                    <img src="/assets/css/images/addmoto.png" style={{ width: '30px', height: '30px' }} alt="" />
                    Adicionar Motocicleta
                </Link>
            </div>

            <div className="search-container" style={{ background: 'rgba(255,255,255,0.03)', padding: '20px', borderRadius: '15px', backdropFilter: 'blur(5px)' }}>
                <input
                    type="text"
                    className="search-input"
                    placeholder="Pesquisar por modelo, marca, placa, proprietário..."
                    value={search}
                    onChange={(e) => setSearch(e.target.value)}
                    onKeyDown={handleSearch}
                    style={{ fontSize: '1.1rem' }}
                />
                <button className="button" onClick={() => { setPage(1); fetchMotos(); }} style={{ padding: '0 30px' }}>Pesquisar</button>
            </div>

            <div className="table-wrapper" style={{ marginTop: '40px', background: 'transparent', padding: 0 }}>
                <table className="measurements-table" style={{ background: 'rgba(255,255,255,0.02)', borderRadius: '15px', overflow: 'hidden' }}>
                    <thead>
                        <tr className="section-header" style={{ background: 'rgba(228, 76, 101, 0.1)' }}>
                            <td style={{ padding: '20px', fontWeight: 'bold' }}>Foto</td>
                            <td onClick={() => toggleSort('endereco')} style={{ cursor: 'pointer', padding: '20px', fontWeight: 'bold' }}>
                                Endereço {orderby === 'endereco' ? (orderDir === 'ASC' ? '↑' : '↓') : ''}
                            </td>
                            <td onClick={() => toggleSort('ano')} style={{ cursor: 'pointer', padding: '20px', fontWeight: 'bold' }}>
                                Ano {orderby === 'ano' ? (orderDir === 'ASC' ? '↑' : '↓') : ''}
                            </td>
                            <td onClick={() => toggleSort('modelo')} style={{ cursor: 'pointer', padding: '20px', fontWeight: 'bold' }}>
                                Modelo {orderby === 'modelo' ? (orderDir === 'ASC' ? '↑' : '↓') : ''}
                            </td>
                            <td onClick={() => toggleSort('marca')} style={{ cursor: 'pointer', padding: '20px', fontWeight: 'bold' }}>
                                Marca {orderby === 'marca' ? (orderDir === 'ASC' ? '↑' : '↓') : ''}
                            </td>
                            <td onClick={() => toggleSort('placa')} style={{ cursor: 'pointer', padding: '20px', fontWeight: 'bold' }}>
                                Placa {orderby === 'placa' ? (orderDir === 'ASC' ? '↑' : '↓') : ''}
                            </td>
                            <td onClick={() => toggleSort('km')} style={{ cursor: 'pointer', padding: '20px', fontWeight: 'bold' }}>
                                KM {orderby === 'km' ? (orderDir === 'ASC' ? '↑' : '↓') : ''}
                            </td>
                            <td onClick={() => toggleSort('proprietario')} style={{ cursor: 'pointer', padding: '20px', fontWeight: 'bold' }}>
                                Proprietário {orderby === 'proprietario' ? (orderDir === 'ASC' ? '↑' : '↓') : ''}
                            </td>
                            <td style={{ textAlign: 'center', padding: '20px', fontWeight: 'bold' }}>Ações</td>
                        </tr>
                    </thead>
                    <tbody>
                        {loading ? (
                            <tr>
                                <td colSpan="9" style={{ textAlign: 'center', padding: '100px', fontSize: '1.2rem', opacity: 0.5 }}>
                                    Carregando dados do sistema...
                                </td>
                            </tr>
                        ) : motos.length > 0 ? (
                            motos.map(moto => (
                                <tr
                                    key={moto.motoId}
                                    style={{ borderBottom: '1px solid rgba(255,255,255,0.05)' }}
                                    onMouseOver={(e) => e.currentTarget.style.background = 'rgba(255,255,255,0.02)'}
                                    onMouseOut={(e) => e.currentTarget.style.background = 'transparent'}
                                >
                                    <td style={{ padding: '15px' }}>
                                        {moto.foto && (
                                            <img
                                                src={getImageUrl(moto.foto)}
                                                alt={moto.modelo}
                                                style={{ width: '50px', height: '50px', objectFit: 'cover', borderRadius: '5px' }}
                                                onError={(e) => { e.target.style.display = 'none'; }}
                                            />
                                        )}
                                    </td>
                                    <td style={{ padding: '20px', fontSize: '1rem' }}>{moto.endereco}</td>
                                    <td style={{ padding: '20px', fontSize: '1rem' }}>{moto.ano}</td>
                                    <td style={{ padding: '20px', fontSize: '1rem' }}>{moto.modelo}</td>
                                    <td style={{ padding: '20px', fontSize: '1rem' }}>{moto.marca}</td>
                                    <td style={{ padding: '20px', fontSize: '1rem' }}>{moto.placa}</td>
                                    <td style={{ padding: '20px', fontSize: '1rem' }}>{formatKM(moto.km)}</td>
                                    <td style={{ padding: '20px', fontSize: '1rem' }}>{moto.proprietario}</td>
                                    <td>
                                        <div style={{ display: 'flex', gap: '15px', justifyContent: 'center', alignItems: 'center' }}>
                                            <Link
                                                href={`/editmotos?motoID=${moto.motoId}`}
                                                style={{ background: 'none', border: 'none', cursor: 'pointer', padding: 0, transition: 'transform 0.2s', display: 'inline-block' }}
                                                title="Editar"
                                            >
                                                <div
                                                    onMouseOver={(e) => e.currentTarget.style.transform = 'scale(1.2)'}
                                                    onMouseOut={(e) => e.currentTarget.style.transform = 'scale(1)'}
                                                    style={{ transition: 'transform 0.2s' }}
                                                >
                                                    <img
                                                        src="/assets/css/images/edit-new.png"
                                                        style={{ height: '28px', width: '38px', filter: 'drop-shadow(0 2px 5px rgba(0,0,0,0.5))' }}
                                                        alt="Editar"
                                                    />
                                                </div>
                                            </Link>
                                            <button
                                                onClick={() => handleDeleteClick(moto)}
                                                style={{ background: 'none', border: 'none', cursor: 'pointer', padding: 0, transition: 'transform 0.2s' }}
                                                onMouseOver={(e) => e.currentTarget.style.transform = 'scale(1.2)'}
                                                onMouseOut={(e) => e.currentTarget.style.transform = 'scale(1)'}
                                                title="Excluir"
                                            >
                                                <img
                                                    src="/assets/css/images/x-button-new.png"
                                                    style={{ height: '28px', width: '38px', filter: 'drop-shadow(0 2px 5px rgba(0,0,0,0.5))' }}
                                                    alt="Excluir"
                                                />
                                            </button>
                                            <Link
                                                href={`/gerenciarfotos?motoID=${moto.motoId}`}
                                                style={{ background: 'none', border: 'none', cursor: 'pointer', padding: 0, transition: 'transform 0.2s', display: 'inline-block' }}
                                                title="Abrir Galeria"
                                            >
                                                <div
                                                    onMouseOver={(e) => e.currentTarget.style.transform = 'scale(1.2)'}
                                                    onMouseOut={(e) => e.currentTarget.style.transform = 'scale(1)'}
                                                    style={{ transition: 'transform 0.2s' }}
                                                >
                                                    <img
                                                        src="/assets/css/images/gallery-button.png"
                                                        style={{ height: '28px', width: '38px', filter: 'drop-shadow(0 2px 5px rgba(0,0,0,0.5))' }}
                                                        alt="Galeria"
                                                    />
                                                </div>
                                            </Link>
                                        </div>
                                    </td>
                                </tr>
                            ))
                        ) : (
                            <tr>
                                <td colSpan="9" style={{ textAlign: 'center', padding: '100px', opacity: 0.5 }}>
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
            {showDeleteModal && motoToDelete && (
                <div className="modal-overlay" onClick={() => setShowDeleteModal(false)}>
                    <div className="modal-content" onClick={(e) => e.stopPropagation()} style={{ maxWidth: '400px', padding: '30px', textAlign: 'center' }}>
                        <h2 style={{ color: '#e44c65', marginBottom: '15px' }}>Confirmar Exclusão</h2>
                        <p style={{ marginBottom: '30px', fontSize: '1.1rem' }}>
                            Deseja realmente excluir a motocicleta de <br />
                            <strong style={{ color: 'white' }}>"{motoToDelete.proprietario}"</strong>?
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
