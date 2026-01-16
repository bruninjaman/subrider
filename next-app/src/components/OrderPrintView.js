import { formatCurrency, formatDate } from '@/lib/utils';
import React from 'react';

export default function OrderPrintView({ order, motorcycle, items }) {
    if (!order) return null;

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
        if (item.Categoria === '2' && item.Codigo && item.Codigo !== '0') content.push(`(${item.Codigo})`);
        return content.join(' - ') || 'N/A';
    };

    return (
        <div className="only-print order-print-view">
            {/* Header */}
            <table className="print-table print-header-table">
                <thead>
                    <tr>
                        <th colSpan="6" className="logo-th">
                            <img src="/assets/css/images/logo-black.png" alt="Subrider" className="print-logo" onError={(e) => {
                                // Fallback if black logo doesn't exist, try original and filter
                                e.target.src = "/assets/css/images/logo.png";
                            }} />
                            <div className="order-number">{order.Codigo}</div>
                        </th>
                    </tr>
                    <tr>
                        <th>Data:</th>
                        <th className="head-content" colSpan="3">{formatDate(order.Data)}</th>
                        <th>Km:</th>
                        <th className="head-content">{order.KM}</th>
                    </tr>
                    <tr>
                        <th colSpan="1">Nome:</th>
                        <th colSpan="3" className="head-content">{order.proprietario_ordem}</th>
                        <th>Fone:</th>
                        <th className="head-content">{motorcycle?.contato || '---'}</th>
                    </tr>
                    <tr>
                        <th colSpan="1">Endereço:</th>
                        <th colSpan="5" className="head-content">{motorcycle?.endereco || '---'}</th>
                    </tr>
                    <tr>
                        <th colSpan="1">Marca:</th>
                        <th colSpan="2" className="head-content">{motorcycle?.marca || '---'}</th>
                        <th colSpan="1">Placa:</th>
                        <th colSpan="2" className="head-content">{motorcycle?.placa || '---'}</th>
                    </tr>
                    <tr>
                        <th colSpan="1">Modelo:</th>
                        <th colSpan="2" className="head-content">{motorcycle?.modelo || '---'}</th>
                        <th colSpan="1">Ano:</th>
                        <th colSpan="2" className="head-content">{motorcycle?.ano || '---'}</th>
                    </tr>
                </thead>
            </table>

            {/* Serviços */}
            <div className="section-title">Serviços</div>
            <table className="print-table">
                <thead>
                    <tr>
                        <th width="45%" className="main-th">Descrição</th>
                        <th width="15%" className="main-th">Quantidade</th>
                        <th width="20%" className="main-th">Valor unitário</th>
                        <th width="20%" className="main-th">Valor Total</th>
                    </tr>
                </thead>
                <tbody>
                    {servicos.length > 0 ? servicos.map(item => (
                        <tr key={item.item_ordemID}>
                            <td style={{ textAlign: 'left' }}>{renderItemContent(item)}</td>
                            <td>{item.Quantidade}</td>
                            <td>{formatCurrency(item.Valor)}</td>
                            <td>{formatCurrency(item.Valor * item.Quantidade)}</td>
                        </tr>
                    )) : (
                        <tr><td colSpan="4" style={{ textAlign: 'center' }}>Nenhum serviço adicionado</td></tr>
                    )}
                    <tr className="total-row">
                        <td colSpan="2"></td>
                        <td className="valores-totais">Total Serviços:</td>
                        <td className="valores-totais">{formatCurrency(totalServicos)}</td>
                    </tr>
                </tbody>
            </table>

            {/* Peças */}
            <div className="section-title">Peças</div>
            <table className="print-table">
                <thead>
                    <tr>
                        <th width="45%" className="main-th">Descrição</th>
                        <th width="15%" className="main-th">Quantidade</th>
                        <th width="20%" className="main-th">Valor unitário</th>
                        <th width="20%" className="main-th">Valor Total</th>
                    </tr>
                </thead>
                <tbody>
                    {pecas.length > 0 ? pecas.map(item => (
                        <tr key={item.item_ordemID}>
                            <td style={{ textAlign: 'left' }}>
                                <div className="item-description">
                                    {item.Foto && item.Foto !== '0' ? (
                                        <img src={`/${item.Foto}`} alt="Peça" className="item-image" />
                                    ) : (
                                        <img src="/assets/css/images/peca-padrao.png" alt="Peça" className="item-image" />
                                    )}
                                    <span>{renderItemContent(item)}</span>
                                </div>
                            </td>
                            <td>{item.Quantidade}</td>
                            <td>{formatCurrency(item.Valor)}</td>
                            <td>{formatCurrency(item.Valor * item.Quantidade)}</td>
                        </tr>
                    )) : (
                        <tr><td colSpan="4" style={{ textAlign: 'center' }}>Nenhuma peça adicionada</td></tr>
                    )}
                    <tr className="total-row">
                        <td colSpan="2"></td>
                        <td className="valores-totais">Total Peças:</td>
                        <td className="valores-totais">{formatCurrency(totalPecas)}</td>
                    </tr>
                </tbody>
            </table>

            {/* Adiantamentos */}
            <div className="section-title">Adiantamentos (Pagamentos Recebidos)</div>
            <table className="print-table">
                <thead>
                    <tr>
                        <th width="45%" className="main-th">Descrição</th>
                        <th width="15%" className="main-th">Quantidade</th>
                        <th width="20%" className="main-th">Valor unitário</th>
                        <th width="20%" className="main-th">Valor Total</th>
                    </tr>
                </thead>
                <tbody>
                    {adiantamentos.length > 0 ? adiantamentos.map(item => (
                        <tr key={item.item_ordemID}>
                            <td style={{ textAlign: 'left' }}>{renderItemContent(item) || "Pagamento"}</td>
                            <td>{item.Quantidade}</td>
                            <td>{formatCurrency(item.Valor)}</td>
                            <td>{formatCurrency(item.Valor * item.Quantidade)}</td>
                        </tr>
                    )) : (
                        <tr><td colSpan="4" style={{ textAlign: 'center' }}>Nenhum adiantamento registrado</td></tr>
                    )}
                    <tr className="total-row">
                        <td colSpan="2"></td>
                        <td className="valores-totais">Total Adiantamentos:</td>
                        <td className="valores-totais">{formatCurrency(totalAdiantamentos)}</td>
                    </tr>
                </tbody>
            </table>

            {/* Resumo */}
            <div className="section-title">Resumo</div>
            <table className="print-table resumo-table">
                <tbody>
                    <tr>
                        <td width="60%"><strong>Total de Serviços:</strong></td>
                        <td width="40%">{formatCurrency(totalServicos)}</td>
                    </tr>
                    <tr>
                        <td><strong>Total de Peças:</strong></td>
                        <td>{formatCurrency(totalPecas)}</td>
                    </tr>
                    <tr>
                        <td><strong>Valor Total:</strong></td>
                        <td>{formatCurrency(totalGeral)}</td>
                    </tr>
                    <tr>
                        <td><strong>Total de Adiantamentos:</strong></td>
                        <td>{formatCurrency(totalAdiantamentos)}</td>
                    </tr>
                    <tr className="final-total">
                        <td><strong>Total a Pagar:</strong></td>
                        <td className="saldo-value">{formatCurrency(saldo)}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    );
}
