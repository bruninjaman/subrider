'use client';

import React, { useState, useEffect, useRef, Suspense } from 'react';
import { useSearchParams } from 'next/navigation';
import RelatorioHeader from './RelatorioHeader';
import Footer from '@/components/Footer';
import './relatorio.css';
import jsPDF from 'jspdf';
import html2canvas from 'html2canvas';

function RelatorioContent() {
    const searchParams = useSearchParams();
    const ordemId = searchParams.get('ordem');

    const [loading, setLoading] = useState(true);
    const [saving, setSaving] = useState(false);
    const [orderData, setOrderData] = useState(null);
    const [motorcycleData, setMotorcycleData] = useState(null);
    const [status, setStatus] = useState({ message: '', type: '' });
    const [dataConclusao, setDataConclusao] = useState(new Date().toISOString().split('T')[0]);

    const editorRef = useRef(null);
    const obsEditorRef = useRef(null);
    const statusTimeoutRef = useRef(null);

    useEffect(() => {
        if (ordemId) {
            fetchData();
        }
    }, [ordemId]);

    const fetchData = async () => {
        setLoading(true);
        try {
            // Fetch order and motorcycle info
            const resInfo = await fetch(`/api/ordemservico/${ordemId}`);
            if (!resInfo.ok) throw new Error('Falha ao carregar informações da ordem');
            const dataInfo = await resInfo.json();
            setOrderData(dataInfo.order);
            setMotorcycleData(dataInfo.motorcycle);

            // Fetch report content
            const resReport = await fetch(`/api/relatorio?ordem=${ordemId}`);
            if (resReport.ok) {
                const dataReport = await resReport.json();
                if (dataReport.status === 'success') {
                    if (editorRef.current) editorRef.current.innerHTML = dataReport.conteudo;
                    if (obsEditorRef.current) obsEditorRef.current.innerHTML = dataReport.observacoes_finais;
                    if (dataReport.data_conclusao) setDataConclusao(dataReport.data_conclusao);
                    showStatus('Relatório carregado com sucesso', 'success');
                } else if (dataReport.status === 'novo') {
                    // Set default template if new
                    setDefaultTemplate(dataInfo.order, dataInfo.motorcycle);
                    showStatus('Novo relatório. Preencha os detalhes.', 'info');
                }
            }
        } catch (error) {
            console.error(error);
            showStatus('Erro ao carregar dados: ' + error.message, 'error');
        } finally {
            setLoading(false);
        }
    };

    const setDefaultTemplate = (order, moto) => {
        if (!editorRef.current) return;

        const motoLabel = moto ? `${moto.marca} ${moto.modelo} (${moto.ano || 'N/A'})` : 'N/A';
        const placaLabel = moto ? moto.placa : 'N/A';
        const kmLabel = order.KM ? `${new Intl.NumberFormat('pt-BR').format(order.KM)} km` : 'quilometragem não informada';
        const nextKmLabel = order.KM ? `${new Intl.NumberFormat('pt-BR').format(order.KM + 5000)} km` : 'não informada';

        editorRef.current.innerHTML = `
            <h3>Descrição do Serviço</h3>
            <p>Serviço realizado na motocicleta ${motoLabel}, placa ${placaLabel} com ${kmLabel}.</p>
            <p>Diagnóstico inicial e procedimentos realizados na motocicleta:</p>
            <ul>
                <li>Verificação geral do estado da motocicleta</li>
                <li>Análise dos componentes mecânicos</li>
                <li>Testes de funcionamento</li>
            </ul>
            <p>Recomendações para manutenção futura:</p>
            <ul>
                <li>Próxima revisão em: ${nextKmLabel}</li>
                <li>Verificar níveis de óleo a cada 1.000 km</li>
            </ul>
        `;
    };

    const showStatus = (message, type) => {
        setStatus({ message, type });
        if (statusTimeoutRef.current) clearTimeout(statusTimeoutRef.current);
        statusTimeoutRef.current = setTimeout(() => setStatus({ message: '', type: '' }), 5000);
    };

    const handleCommand = (command) => {
        document.execCommand(command, false, null);
    };

    const handleSave = async () => {
        if (!ordemId) return;
        setSaving(true);
        try {
            const formData = new FormData();
            formData.append('conteudo', editorRef.current.innerHTML);
            formData.append('data_conclusao', dataConclusao);
            formData.append('observacoes_finais', obsEditorRef.current.innerHTML);
            formData.append('quilometragem', orderData?.KM || '');

            const res = await fetch(`/api/relatorio?ordem=${ordemId}`, {
                method: 'POST',
                body: formData
            });

            const data = await res.json();
            if (data.status === 'success') {
                showStatus(data.message, 'success');
            } else {
                showStatus(data.message, 'error');
            }
        } catch (error) {
            showStatus('Erro ao salvar: ' + error.message, 'error');
        } finally {
            setSaving(false);
        }
    };

    const handleGeneratePDF = async () => {
        setSaving(true);
        showStatus('Gerando PDF...', 'info');

        try {
            const numeroOrdem = orderData?.Codigo || 'relatorio';
            const fileName = `relatorio_${numeroOrdem.replace(/\//g, '-')}.pdf`;

            // Create a hidden container for PDF rendering
            const pdfContainer = document.createElement('div');
            pdfContainer.style.width = '210mm';
            pdfContainer.style.padding = '20mm';
            pdfContainer.style.background = 'white';
            pdfContainer.style.color = 'black';
            pdfContainer.style.fontFamily = 'Arial, sans-serif';
            pdfContainer.style.position = 'absolute';
            pdfContainer.style.left = '-9999px';

            const content = `
                <div style="text-align:center; border-bottom: 2px solid #e44c65; padding-bottom: 10px; margin-bottom: 20px;">
                    <h1 style="color: #e44c65; margin-bottom: 5px;">RELATÓRIO DE SERVIÇO</h1>
                    <p style="font-size: 14pt; margin: 0;">Ordem de Serviço: ${numeroOrdem}</p>
                </div>
                
                <table style="width:100%; border-collapse: collapse; margin-bottom: 20px;">
                    <tr>
                        <td style="padding: 5px; font-weight: bold; border-bottom: 1px solid #eee; width: 30%;">Cliente:</td>
                        <td style="padding: 5px; border-bottom: 1px solid #eee;">${motorcycleData?.proprietario || orderData?.proprietario_ordem || 'N/A'}</td>
                    </tr>
                    <tr>
                        <td style="padding: 5px; font-weight: bold; border-bottom: 1px solid #eee;">Motocicleta:</td>
                        <td style="padding: 5px; border-bottom: 1px solid #eee;">${motorcycleData?.marca} ${motorcycleData?.modelo} (${motorcycleData?.ano}) - ${motorcycleData?.placa}</td>
                    </tr>
                    <tr>
                        <td style="padding: 5px; font-weight: bold; border-bottom: 1px solid #eee;">Data:</td>
                        <td style="padding: 5px; border-bottom: 1px solid #eee;">${new Date(orderData?.Data).toLocaleDateString('pt-BR')}</td>
                    </tr>
                    <tr>
                        <td style="padding: 5px; font-weight: bold; border-bottom: 1px solid #eee;">Quilometragem:</td>
                        <td style="padding: 5px; border-bottom: 1px solid #eee;">${orderData?.KM ? new Intl.NumberFormat('pt-BR').format(orderData.KM) + ' km' : 'N/A'}</td>
                    </tr>
                </table>
                
                <div style="margin-bottom: 20px;">
                    <h2 style="color: #e44c65; font-size: 14pt; border-bottom: 1px solid #ccc; padding-bottom: 5px;">DETALHES DO SERVIÇO</h2>
                    <div style="font-size: 11pt; line-height: 1.4;">${editorRef.current.innerHTML}</div>
                </div>
                
                ${obsEditorRef.current.innerHTML ? `
                <div style="margin-top: 20px; border-top: 1px dashed #ccc; padding-top: 10px;">
                    <h3 style="color: #e44c65; font-size: 12pt;">OBSERVAÇÕES FINAIS</h3>
                    <div style="font-size: 11pt; line-height: 1.4;">${obsEditorRef.current.innerHTML}</div>
                </div>
                ` : ''}
                
                <div style="margin-top: 30px; text-align: right;">
                    <p><strong>Concluído em:</strong> ${new Date(dataConclusao).toLocaleDateString('pt-BR')}</p>
                </div>
            `;

            pdfContainer.innerHTML = content;
            document.body.appendChild(pdfContainer);

            const canvas = await html2canvas(pdfContainer, { scale: 2 });
            document.body.removeChild(pdfContainer);

            const imgData = canvas.toDataURL('image/jpeg', 0.95);
            const pdf = new jsPDF('p', 'mm', 'a4');
            const imgProps = pdf.getImageProperties(imgData);
            const pdfWidth = pdf.internal.pageSize.getWidth();
            const pdfHeight = (imgProps.height * pdfWidth) / imgProps.width;

            pdf.addImage(imgData, 'JPEG', 0, 0, pdfWidth, pdfHeight);
            pdf.save(fileName);

            showStatus('PDF gerado com sucesso!', 'success');
        } catch (error) {
            console.error(error);
            showStatus('Erro ao gerar PDF: ' + error.message, 'error');
        } finally {
            setSaving(false);
        }
    };

    if (!ordemId) {
        return (
            <div className="relatorioPage">
                <RelatorioHeader />
                <div className="relatorioContainer" style={{ textAlign: 'center', padding: '5rem' }}>
                    <h1>ID da Ordem não fornecido</h1>
                    <p>Por favor, acesse o relatório através da tabela de ordens.</p>
                </div>
                <Footer />
            </div>
        );
    }

    return (
        <div className="relatorioPage">
            <RelatorioHeader />

            <div className="relatorioContainer">
                <div className="formRelatorio">
                    <div className="formHeader">
                        <img className="logoRelatorio" src="/assets/css/images/logo-branco-crop.png" alt="Logo" />
                        <h1>Relatório de Ordem de Serviço</h1>
                    </div>

                    {loading ? (
                        <div style={{ padding: '5rem', textAlign: 'center' }}>
                            <div className="loader" style={{ margin: '0 auto' }}></div>
                            <p>Carregando informações...</p>
                        </div>
                    ) : (
                        <>
                            <div className="formSection">
                                <h2>Informações da Ordem</h2>
                                <div className="formRow">
                                    <div className="formGroup col4">
                                        <label>Nº da Ordem:</label>
                                        <input type="text" className="formControl" value={orderData?.Codigo || ''} readOnly />
                                    </div>
                                    <div className="formGroup col4">
                                        <label>Data:</label>
                                        <input type="text" className="formControl" value={orderData?.Data ? new Date(orderData.Data).toLocaleDateString('pt-BR') : ''} readOnly />
                                    </div>
                                    <div className="formGroup col4">
                                        <label>Quilometragem:</label>
                                        <input type="text" className="formControl" value={orderData?.KM ? new Intl.NumberFormat('pt-BR').format(orderData.KM) : ''} readOnly />
                                    </div>
                                </div>

                                <div className="formRow">
                                    <div className="formGroup col6">
                                        <label>Cliente:</label>
                                        <textarea className="formControl" value={motorcycleData?.proprietario || orderData?.proprietario_ordem || ''} readOnly />
                                    </div>
                                    <div className="formGroup col6">
                                        <label>Motocicleta:</label>
                                        <textarea
                                            className="formControl"
                                            value={motorcycleData ? `${motorcycleData.marca} ${motorcycleData.modelo} (${motorcycleData.ano}) - ${motorcycleData.placa}` : ''}
                                            readOnly
                                        />
                                    </div>
                                </div>

                                {motorcycleData?.endereco && (
                                    <div className="formRow">
                                        <div className="formGroup col12">
                                            <label>Endereço:</label>
                                            <textarea className="formControl" value={motorcycleData.endereco} readOnly rows={1} />
                                        </div>
                                    </div>
                                )}
                            </div>

                            <div className="formSection">
                                <h2>Detalhes do Serviço</h2>
                                <div className="formGroup">
                                    <div
                                        ref={editorRef}
                                        className="editorPersonalizado"
                                        contentEditable={true}
                                        suppressContentEditableWarning={true}
                                    >
                                    </div>
                                </div>
                                <div className="editorToolbar">
                                    <button type="button" onClick={() => handleCommand('bold')}><strong>B</strong></button>
                                    <button type="button" onClick={() => handleCommand('underline')}><u>U</u></button>
                                    <button type="button" onClick={() => handleCommand('insertUnorderedList')}>• List</button>
                                    <button type="button" onClick={() => handleCommand('insertOrderedList')}>1. List</button>
                                </div>
                            </div>

                            <div className="formSection">
                                <h2>Finalização</h2>
                                <div className="formRow">
                                    <div className="formGroup col6">
                                        <label>Data de Conclusão:</label>
                                        <input
                                            type="date"
                                            className="formControl"
                                            value={dataConclusao}
                                            onChange={(e) => setDataConclusao(e.target.value)}
                                        />
                                    </div>
                                </div>
                                <div className="formRow">
                                    <div className="formGroup col12">
                                        <label>Observações Finais:</label>
                                        <div
                                            ref={obsEditorRef}
                                            className="editorPersonalizado"
                                            style={{ minHeight: '100px' }}
                                            contentEditable={true}
                                            suppressContentEditableWarning={true}
                                            placeholder="Observações finais..."
                                        >
                                        </div>
                                        <div className="editorToolbar" style={{ marginTop: '10px' }}>
                                            <button type="button" onClick={() => handleCommand('bold')}><strong>B</strong></button>
                                            <button type="button" onClick={() => handleCommand('underline')}><u>U</u></button>
                                            <button type="button" onClick={() => handleCommand('insertUnorderedList')}>• List</button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div className="formActions">
                                <button type="button" className="button buttonPrimary" onClick={handleSave} disabled={saving}>
                                    {saving ? 'Salvando...' : 'Salvar Relatório'}
                                </button>
                                <button type="button" className="button" onClick={handleGeneratePDF} disabled={saving}>
                                    Download PDF
                                </button>
                                <div className={`statusMessage ${status.message ? 'visible' : ''} ${status.type}`}>
                                    {status.message}
                                </div>
                            </div>
                        </>
                    )}
                </div>
            </div>

            {saving && (
                <div className="loaderOverlay">
                    <div className="loader"></div>
                    <p style={{ color: 'white' }}>Processando, aguarde...</p>
                </div>
            )}

            <Footer />
        </div>
    );
}

export default function RelatorioPage() {
    return (
        <Suspense fallback={<div>Carregando...</div>}>
            <RelatorioContent />
        </Suspense>
    );
}
