import { formatCurrency } from '@/lib/utils';

export default function ItemsTable({ items }) {
    const servicos = items.filter(i => i.Categoria == '1' || (i.Categoria != '2' && i.Categoria != '3'));
    const pecas = items.filter(i => i.Categoria == '2');
    const adiantamentos = items.filter(i => i.Categoria == '3');

    const totalServicos = servicos.reduce((acc, curr) => acc + (curr.Valor * curr.Quantidade), 0);
    const totalPecas = pecas.reduce((acc, curr) => acc + (curr.Valor * curr.Quantidade), 0);
    const totalAdiantamentos = adiantamentos.reduce((acc, curr) => acc + (curr.Valor * curr.Quantidade), 0);
    const totalGeral = totalServicos + totalPecas;
    const saldo = totalGeral - totalAdiantamentos;

    const renderItemContent = (item) => {
        let content = [];
        if (item.Tipo && item.Tipo !== '0') content.push(item.Tipo);
        if (item.Grupo && item.Grupo !== '0') content.push(item.Grupo);
        if (item.Item && item.Item !== '0') content.push(item.Item);
        if (item.Parte && item.Parte !== '0') content.push(item.Parte);
        if (item.Descricao && item.Descricao !== '0') content.push(item.Descricao);
        return content.join(' - ') || 'N/A';
    };

    const handleDelete = async (item) => {
        const description = renderItemContent(item);
        if (window.confirm(`Deseja realmente excluir o item: "${description}"?`)) {
            try {
                const res = await fetch(`/api/ordemservico/items/${item.item_ordemID}`, {
                    method: 'DELETE'
                });
                if (res.ok) {
                    alert('Item excluído com sucesso!');
                    window.location.reload();
                } else {
                    throw new Error('Falha ao excluir');
                }
            } catch (err) {
                alert('Erro ao excluir item: ' + err.message);
            }
        }
    };

    const renderActions = (item) => (
        <div style={{ display: 'flex', gap: '15px', justifyContent: 'center' }}>
            <button onClick={() => window.location.href = `/ordem_edit_item?item_ordemID=${item.item_ordemID}&ordem=${item.Ordem}`} style={{ background: 'none', border: 'none', cursor: 'pointer', padding: 0 }}>
                <img src="/assets/css/images/edit.png" style={{ height: '22px', width: '22px', filter: 'brightness(1.5)' }} title="Editar" alt="Editar" />
            </button>
            <button onClick={() => handleDelete(item)} style={{ background: 'none', border: 'none', cursor: 'pointer', padding: 0 }}>
                <img src="/assets/css/images/x-button.png" style={{ height: '22px', width: '22px' }} title="Excluir" alt="Excluir" />
            </button>
        </div>
    );

    return (
        <div className="table-wrapper" style={{ background: 'rgba(255,255,255,0.02)', padding: '0', overflow: 'hidden' }}>
            {/* Serviços */}
            <div className="table-section" style={{ marginBottom: '40px' }}>
                <div style={{ background: 'rgba(255,255,255,0.05)', padding: '15px 25px', color: '#e44c65', fontWeight: '600', fontSize: '1.2rem', display: 'flex', justifyContent: 'space-between' }}>
                    <span>SERVIÇOS</span>
                    <span style={{ opacity: 0.6, fontWeight: '400', fontSize: '0.9rem' }}>{servicos.length} itens</span>
                </div>
                <table className="measurements-table">
                    <thead>
                        <tr style={{ background: 'rgba(0,0,0,0.3)' }}>
                            <td style={{ color: '#ccc', fontSize: '0.85rem' }}>DESCRIÇÃO</td>
                            <td style={{ textAlign: 'center', color: '#ccc', fontSize: '0.85rem' }}>QTD</td>
                            <td style={{ textAlign: 'center', color: '#ccc', fontSize: '0.85rem' }}>UNITÁRIO</td>
                            <td style={{ textAlign: 'center', color: '#ccc', fontSize: '0.85rem' }}>TOTAL</td>
                            <td style={{ textAlign: 'center', color: '#ccc', fontSize: '0.85rem' }}>AÇÕES</td>
                        </tr>
                    </thead>
                    <tbody>
                        {servicos.length > 0 ? servicos.map(item => (
                            <tr key={item.item_ordemID}>
                                <td style={{ color: 'white' }}>{renderItemContent(item)}</td>
                                <td style={{ textAlign: 'center' }}>{item.Quantidade}</td>
                                <td style={{ textAlign: 'center' }}>{formatCurrency(item.Valor)}</td>
                                <td style={{ textAlign: 'center', fontWeight: 'bold' }}>{formatCurrency(item.Valor * item.Quantidade)}</td>
                                <td>{renderActions(item)}</td>
                            </tr>
                        )) : (
                            <tr><td colSpan="5" style={{ textAlign: 'center', padding: '30px', opacity: 0.5 }}>Nenhum serviço registrado</td></tr>
                        )}
                    </tbody>
                    <tfoot>
                        <tr style={{ background: 'rgba(255,255,255,0.02)' }}>
                            <td colSpan="3" style={{ textAlign: 'right', padding: '15px 25px', fontWeight: '500' }}>Total Serviços:</td>
                            <td style={{ textAlign: 'center', fontWeight: '700', color: '#e44c65' }}>{formatCurrency(totalServicos)}</td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            {/* Peças */}
            <div className="table-section" style={{ marginBottom: '40px' }}>
                <div style={{ background: 'rgba(255,255,255,0.05)', padding: '15px 25px', color: '#e44c65', fontWeight: '600', fontSize: '1.2rem', display: 'flex', justifyContent: 'space-between' }}>
                    <span>PEÇAS</span>
                    <span style={{ opacity: 0.6, fontWeight: '400', fontSize: '0.9rem' }}>{pecas.length} itens</span>
                </div>
                <table className="measurements-table">
                    <thead>
                        <tr style={{ background: 'rgba(0,0,0,0.3)' }}>
                            <td style={{ color: '#ccc', fontSize: '0.85rem' }}>DESCRIÇÃO</td>
                            <td style={{ textAlign: 'center', color: '#ccc', fontSize: '0.85rem' }}>QTD</td>
                            <td style={{ textAlign: 'center', color: '#ccc', fontSize: '0.85rem' }}>UNITÁRIO</td>
                            <td style={{ textAlign: 'center', color: '#ccc', fontSize: '0.85rem' }}>TOTAL</td>
                            <td style={{ textAlign: 'center', color: '#ccc', fontSize: '0.85rem' }}>AÇÕES</td>
                        </tr>
                    </thead>
                    <tbody>
                        {pecas.length > 0 ? pecas.map(item => (
                            <tr key={item.item_ordemID}>
                                <td>
                                    <div style={{ display: 'flex', alignItems: 'center' }}>
                                        {item.Foto && item.Foto !== '0' && (
                                            <img src={`/${item.Foto}`} alt="" style={{ width: '60px', height: '60px', objectFit: 'cover', borderRadius: '10px', marginRight: '15px', boxShadow: '0 4px 10px rgba(0,0,0,0.3)' }} />
                                        )}
                                        <div>
                                            <div style={{ color: 'white', fontWeight: '500' }}>{renderItemContent(item)}</div>
                                            {item.Codigo && item.Codigo !== '0' && <small style={{ display: 'block', opacity: 0.6, fontSize: '0.75rem' }}>CÓD: {item.Codigo}</small>}
                                        </div>
                                    </div>
                                </td>
                                <td style={{ textAlign: 'center' }}>{item.Quantidade}</td>
                                <td style={{ textAlign: 'center' }}>{formatCurrency(item.Valor)}</td>
                                <td style={{ textAlign: 'center', fontWeight: 'bold' }}>{formatCurrency(item.Valor * item.Quantidade)}</td>
                                <td>{renderActions(item)}</td>
                            </tr>
                        )) : (
                            <tr><td colSpan="5" style={{ textAlign: 'center', padding: '30px', opacity: 0.5 }}>Nenhuma peça registrada</td></tr>
                        )}
                    </tbody>
                    <tfoot>
                        <tr style={{ background: 'rgba(255,255,255,0.02)' }}>
                            <td colSpan="3" style={{ textAlign: 'right', padding: '15px 25px', fontWeight: '500' }}>Total Peças:</td>
                            <td style={{ textAlign: 'center', fontWeight: '700', color: '#e44c65' }}>{formatCurrency(totalPecas)}</td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            {/* Resumo Financeiro */}
            <div style={{
                background: 'linear-gradient(135deg, #1c1d26 0%, #24262e 100%)',
                padding: '30px',
                borderRadius: '12px',
                margin: '20px',
                border: '1px solid rgba(255,255,255,0.05)',
                boxShadow: '0 10px 30px rgba(0,0,0,0.2)'
            }}>
                <h3 style={{ color: '#e44c65', marginBottom: '20px', fontSize: '1.4rem' }}>RESUMO FINANCEIRO</h3>
                <div style={{ display: 'flex', flexDirection: 'column', gap: '15px' }}>
                    <div style={{ display: 'flex', justifyContent: 'space-between', opacity: 0.8 }}>
                        <span>Total Serviços</span>
                        <span>{formatCurrency(totalServicos)}</span>
                    </div>
                    <div style={{ display: 'flex', justifyContent: 'space-between', opacity: 0.8 }}>
                        <span>Total Peças</span>
                        <span>{formatCurrency(totalPecas)}</span>
                    </div>
                    <div style={{ display: 'flex', justifyContent: 'space-between', color: '#e44c65' }}>
                        <span>Adiantamentos</span>
                        <span style={{ fontWeight: '600' }}>- {formatCurrency(totalAdiantamentos)}</span>
                    </div>
                    <div style={{
                        display: 'flex',
                        justifyContent: 'space-between',
                        marginTop: '15px',
                        paddingTop: '15px',
                        borderTop: '2px solid rgba(255,255,255,0.1)',
                        fontSize: '1.5rem',
                        fontWeight: '700',
                        color: 'white'
                    }}>
                        <span>SALDO A PAGAR</span>
                        <span style={{ color: '#e44c65' }}>{formatCurrency(saldo)}</span>
                    </div>
                </div>
            </div>
        </div>
    );
}
