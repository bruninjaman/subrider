'use client';

import { useState, useEffect } from 'react';
import Link from 'next/link';

export default function Home() {
  const [orders, setOrders] = useState([]);
  const [search, setSearch] = useState('');
  const [loading, setLoading] = useState(true);
  const [orderby, setOrderby] = useState('ordem_servicos.servID');
  const [orderDir, setOrderDir] = useState('DESC');

  const fetchOrders = async () => {
    setLoading(true);
    try {
      const res = await fetch(`/api/ordemservico?q=${encodeURIComponent(search)}&orderby=${orderby}&order=${orderDir}`);
      const data = await res.json();
      if (Array.isArray(data)) {
        setOrders(data);
      }
    } catch (err) {
      console.error(err);
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    fetchOrders();
  }, [orderby, orderDir]);

  const handleSearch = (e) => {
    if (e.key === 'Enter') {
      fetchOrders();
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

  const handleDelete = async (id) => {
    if (confirm(`Deseja realmente excluir a ordem ${id}?`)) {
      alert('Funcionalidade de exclusão em breve.');
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
        <h1 style={{ margin: 0, fontSize: '2.5rem' }}>Gerenciamento de Ordens</h1>
        <Link href="/ordem_add" className="button" style={{ boxShadow: '0 4px 15px rgba(228, 76, 101, 0.3)' }}>Gerar Ordem de Serviço</Link>
      </div>

      <div className="search-container" style={{ background: 'rgba(255,255,255,0.03)', padding: '20px', borderRadius: '15px', backdropFilter: 'blur(5px)' }}>
        <input
          type="text"
          className="search-input"
          placeholder="Pesquisar por modelo, marca, proprietário ou código..."
          value={search}
          onChange={(e) => setSearch(e.target.value)}
          onKeyDown={handleSearch}
          style={{ fontSize: '1.1rem' }}
        />
        <button className="button" onClick={fetchOrders} style={{ padding: '0 30px' }}>Pesquisar</button>
      </div>

      <div className="table-wrapper" style={{ marginTop: '40px', background: 'transparent', padding: 0 }}>
        <table className="measurements-table" style={{ background: 'rgba(255,255,255,0.02)', borderRadius: '15px', overflow: 'hidden' }}>
          <thead>
            <tr className="section-header" style={{ background: 'rgba(228, 76, 101, 0.1)' }}>
              <td style={{ width: '120px', padding: '20px' }}>Foto</td>
              <td onClick={() => toggleSort('Codigo')} style={{ cursor: 'pointer', padding: '20px' }}>
                Ordem {orderby === 'Codigo' ? (orderDir === 'ASC' ? '↑' : '↓') : ''}
              </td>
              <td onClick={() => toggleSort('ano')} style={{ cursor: 'pointer', padding: '20px' }}>
                Ano {orderby === 'ano' ? (orderDir === 'ASC' ? '↑' : '↓') : ''}
              </td>
              <td onClick={() => toggleSort('modelo')} style={{ cursor: 'pointer', padding: '20px' }}>
                Modelo {orderby === 'modelo' ? (orderDir === 'ASC' ? '↑' : '↓') : ''}
              </td>
              <td onClick={() => toggleSort('marca')} style={{ cursor: 'pointer', padding: '20px' }}>
                Marca {orderby === 'marca' ? (orderDir === 'ASC' ? '↑' : '↓') : ''}
              </td>
              <td style={{ padding: '20px' }}>Proprietário</td>
              <td style={{ textAlign: 'center', padding: '20px' }}>Ações</td>
            </tr>
          </thead>
          <tbody>
            {loading ? (
              <tr><td colSpan="7" style={{ textAlign: 'center', padding: '100px', fontSize: '1.2rem', opacity: 0.5 }}>Carregando dados do sistema...</td></tr>
            ) : orders.length > 0 ? (
              orders.map(order => (
                <tr key={order.Codigo} style={{ borderBottom: '1px solid rgba(255,255,255,0.05)' }} onMouseOver={(e) => e.currentTarget.style.background = 'rgba(255,255,255,0.02)'} onMouseOut={(e) => e.currentTarget.style.background = 'transparent'}>
                  <td className="img-table" style={{ padding: '15px' }}>
                    {order.foto ? <img src={order.foto.startsWith('http') ? order.foto : `/${order.foto}`} alt="" style={{ width: '80px', height: '80px', borderRadius: '12px', boxShadow: '0 4px 10px rgba(0,0,0,0.3)' }} /> : <div style={{ width: '80px', height: '80px', background: 'rgba(255,255,255,0.05)', borderRadius: '12px', display: 'flex', alignItems: 'center', justifyContent: 'center', fontSize: '0.8rem' }}>SEM FOTO</div>}
                  </td>
                  <td style={{ fontWeight: '700', color: 'white', fontSize: '1.1rem' }}>{order.Codigo}</td>
                  <td>{order.ano}</td>
                  <td style={{ fontWeight: '500' }}>{order.modelo}</td>
                  <td>{order.marca}</td>
                  <td>{order.proprietario_ordem || order.proprietario}</td>
                  <td>
                    <div style={{ display: 'flex', gap: '20px', justifyContent: 'center', alignItems: 'center' }}>
                      <Link href={`/ordemservico/${order.Codigo}`} style={{ color: 'white', fontSize: '1.5rem', textDecoration: 'none', transition: 'transform 0.2s' }} onMouseOver={(e) => e.currentTarget.style.transform = 'scale(1.2)'} onMouseOut={(e) => e.currentTarget.style.transform = 'scale(1)'}>
                        👁️
                      </Link>
                      <button
                        onClick={() => window.location.href = `/tabelaOrdensEdit?ordem=${order.Codigo}`}
                        style={{ background: 'none', border: 'none', cursor: 'pointer', padding: 0, transition: 'transform 0.2s' }}
                        onMouseOver={(e) => e.currentTarget.style.transform = 'scale(1.2)'}
                        onMouseOut={(e) => e.currentTarget.style.transform = 'scale(1)'}
                      >
                        <img src="/assets/css/images/edit-ordem.png" style={{ height: '2.2em', width: '2.2em', filter: 'drop-shadow(0 2px 5px rgba(0,0,0,0.5))' }} alt="Editar" />
                      </button>
                      <button
                        onClick={() => handleDelete(order.Codigo)}
                        style={{ background: 'none', border: 'none', cursor: 'pointer', padding: 0, transition: 'transform 0.2s' }}
                        onMouseOver={(e) => e.currentTarget.style.transform = 'scale(1.2)'}
                        onMouseOut={(e) => e.currentTarget.style.transform = 'scale(1)'}
                      >
                        <img src="/assets/css/images/x-button.png" style={{ height: '30px', width: '30px', filter: 'drop-shadow(0 2px 5px rgba(0,0,0,0.5))' }} alt="Excluir" />
                      </button>
                    </div>
                  </td>
                </tr>
              ))
            ) : (
              <tr><td colSpan="7" style={{ textAlign: 'center', padding: '100px', opacity: 0.5 }}>Nenhuma ordem encontrada para os critérios de busca.</td></tr>
            )}
          </tbody>
        </table>
      </div>
    </div>
  );
}
