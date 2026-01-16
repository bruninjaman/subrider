'use client';

import { useState, useEffect, Suspense, useRef } from 'react';
import { useSearchParams, useRouter } from 'next/navigation';
import styles from '../ordem_add_item/page.module.css';

function EditItemContent() {
    const searchParams = useSearchParams();
    const router = useRouter();
    const ordem = searchParams.get('ordem');
    const itemOrdemId = searchParams.get('item_ordemID');

    const [loading, setLoading] = useState(true);
    const [tipoItem, setTipoItem] = useState('');
    const [parts, setParts] = useState([]);
    const [services, setServices] = useState([]);
    const [isSubmitting, setIsSubmitting] = useState(false);
    const [originalItem, setOriginalItem] = useState(null);

    // Peça form states
    const [selectedPart, setSelectedPart] = useState('');
    const [selectedPartId, setSelectedPartId] = useState('');
    const [scode, setScode] = useState('');
    const [pquantidade, setPquantidade] = useState(1);
    const [pvalor, setPvalor] = useState(0);
    const [filteredParts, setFilteredParts] = useState([]);
    const [showPartSuggestions, setShowPartSuggestions] = useState(false);
    const partInputRef = useRef(null);

    // Serviço form states
    const [selectedService, setSelectedService] = useState('');
    const [selectedServiceId, setSelectedServiceId] = useState('');
    const [squantidade, setSquantidade] = useState(1);
    const [svalor, setSvalor] = useState(0);
    const [filteredServices, setFilteredServices] = useState([]);
    const [showServiceSuggestions, setShowServiceSuggestions] = useState(false);
    const serviceInputRef = useRef(null);

    // Adiantamento form states
    const [aitem, setAitem] = useState('');
    const [avalor, setAvalor] = useState(0);

    // Load item data
    useEffect(() => {
        const fetchItemData = async () => {
            if (!itemOrdemId) return;

            try {
                const res = await fetch(`/api/ordem_edit_item?item_ordemID=${itemOrdemId}`);
                const data = await res.json();

                if (data.item) {
                    const item = data.item;
                    setOriginalItem(item);

                    // Set category/type
                    if (item.Categoria == 2) {
                        setTipoItem('pecas');
                        const partValue = `${item.Grupo} - ${item.Item} - ${item.Parte}`;
                        setSelectedPart(partValue);
                        setScode(item.Codigo || '');
                        setPquantidade(item.Quantidade || 1);
                        setPvalor(item.Valor || 0);
                    } else if (item.Categoria == 1) {
                        setTipoItem('service');
                        const serviceValue = `${item.Tipo} - ${item.Item}`;
                        setSelectedService(serviceValue);
                        setSquantidade(item.Quantidade || 1);
                        setSvalor(item.Valor || 0);
                    } else if (item.Categoria == 3) {
                        setTipoItem('adiantamento');
                        setAitem(item.Descricao || '');
                        setAvalor(item.Valor || 0);
                    }
                }
            } catch (error) {
                console.error('Error fetching item:', error);
            } finally {
                setLoading(false);
            }
        };

        fetchItemData();
    }, [itemOrdemId]);

    // Load parts and services on mount
    useEffect(() => {
        const fetchParts = async () => {
            try {
                const res = await fetch('/api/pecas?limit=1000&orderby=grupo&order=ASC');
                const data = await res.json();
                if (data.items) {
                    setParts(data.items);
                    setFilteredParts(data.items);

                    // Find matching part for selectedPart
                    if (selectedPart && originalItem?.Categoria == 2) {
                        const matchingPart = data.items.find(p =>
                            `${p.grupo} - ${p.item} - ${p.parte}` === selectedPart
                        );
                        if (matchingPart) {
                            setSelectedPartId(matchingPart.pecaId);
                        }
                    }
                }
            } catch (error) {
                console.error('Error fetching parts:', error);
            }
        };

        const fetchServices = async () => {
            try {
                const res = await fetch('/api/servicos?orderby=tipo&order=ASC');
                const data = await res.json();
                if (Array.isArray(data)) {
                    setServices(data);
                    setFilteredServices(data);

                    // Find matching service for selectedService
                    if (selectedService && originalItem?.Categoria == 1) {
                        const matchingService = data.find(s =>
                            `${s.tipo} - ${s.item}` === selectedService
                        );
                        if (matchingService) {
                            setSelectedServiceId(matchingService.servicoId);
                        }
                    }
                }
            } catch (error) {
                console.error('Error fetching services:', error);
            }
        };

        fetchParts();
        fetchServices();
    }, [selectedPart, selectedService, originalItem]);

    // Click outside to close suggestions
    useEffect(() => {
        const handleClickOutside = (e) => {
            if (partInputRef.current && !partInputRef.current.contains(e.target)) {
                setShowPartSuggestions(false);
            }
            if (serviceInputRef.current && !serviceInputRef.current.contains(e.target)) {
                setShowServiceSuggestions(false);
            }
        };
        document.addEventListener('mousedown', handleClickOutside);
        return () => document.removeEventListener('mousedown', handleClickOutside);
    }, []);

    // Filter parts based on search
    const handlePartSearch = (value) => {
        setSelectedPart(value);
        setSelectedPartId('');

        if (value.trim() === '') {
            setFilteredParts(parts);
        } else {
            const searchTerms = value.toLowerCase().split(' ');
            const filtered = parts.filter(part => {
                const partText = `${part.grupo} - ${part.item} - ${part.parte}`.toLowerCase();
                return searchTerms.every(term => partText.includes(term));
            });
            setFilteredParts(filtered);
        }
        setShowPartSuggestions(true);
    };

    // Select a part from suggestions
    const selectPart = (part) => {
        const displayValue = `${part.grupo} - ${part.item} - ${part.parte}`;
        setSelectedPart(displayValue);
        setSelectedPartId(part.pecaId);
        setShowPartSuggestions(false);
    };

    // Filter services based on search
    const handleServiceSearch = (value) => {
        setSelectedService(value);
        setSelectedServiceId('');

        if (value.trim() === '') {
            setFilteredServices(services);
        } else {
            const searchTerms = value.toLowerCase().split(' ');
            const filtered = services.filter(service => {
                const serviceText = `${service.tipo} - ${service.item}`.toLowerCase();
                return searchTerms.every(term => serviceText.includes(term));
            });
            setFilteredServices(filtered);
        }
        setShowServiceSuggestions(true);
    };

    // Select a service from suggestions
    const selectService = (service) => {
        const displayValue = `${service.tipo} - ${service.item}`;
        setSelectedService(displayValue);
        setSelectedServiceId(service.servicoId);
        setShowServiceSuggestions(false);
    };

    const handleSubmit = async (e) => {
        e.preventDefault();

        if (!ordem || !itemOrdemId) {
            alert('Ordem ID ou Item ID está faltando');
            return;
        }

        if (!tipoItem) {
            alert('Selecione o tipo de item');
            return;
        }

        setIsSubmitting(true);

        const payload = {
            tipo_item: tipoItem,
            ordem,
            item_ordemID: itemOrdemId
        };

        if (tipoItem === 'pecas') {
            if (!selectedPartId) {
                alert('Selecione uma peça válida da lista');
                setIsSubmitting(false);
                return;
            }
            payload.pecaid = selectedPartId;
            payload.pquantidade = pquantidade;
            payload.pvalor = pvalor;
            payload.scode = scode;
        } else if (tipoItem === 'service') {
            if (!selectedServiceId) {
                alert('Selecione um serviço válido da lista');
                setIsSubmitting(false);
                return;
            }
            payload.servicoid = selectedServiceId;
            payload.squantidade = squantidade;
            payload.svalor = svalor;
        } else if (tipoItem === 'adiantamento') {
            if (!aitem.trim()) {
                alert('Preencha a descrição do adiantamento');
                setIsSubmitting(false);
                return;
            }
            payload.avalor = avalor;
            payload.aitem = aitem;
        }

        try {
            const res = await fetch('/api/ordem_edit_item', {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload)
            });
            const data = await res.json();

            if (data.success) {
                router.push(`/ordemservico/${ordem}`);
            } else {
                alert('Erro ao editar item: ' + data.error);
            }
        } catch (error) {
            console.error('Submit error:', error);
            alert('Erro ao enviar formulário');
        } finally {
            setIsSubmitting(false);
        }
    };

    const handleGoBack = () => {
        router.push(`/ordemservico/${ordem}`);
    };

    if (loading) {
        return (
            <div className={styles.loading}>
                <div className={styles.loadingSpinner}></div>
                <p>Carregando dados do item...</p>
            </div>
        );
    }

    return (
        <section className={styles.container}>
            <div className={styles.content}>
                <div className={styles.header}>
                    <img
                        src="/assets/css/images/logo-branco-crop.png"
                        alt="Logo"
                        className={styles.logo}
                    />
                    <h2 className={styles.title}>Editar Item da Ordem</h2>
                    <p className={styles.subtitle}>Ordem: <strong>{ordem}</strong> | Item ID: <strong>{itemOrdemId}</strong></p>
                </div>

                <form onSubmit={handleSubmit}>
                    {/* Tipo de Item Selection */}
                    <div className={styles.typeSelection}>
                        <h3 className={styles.sectionTitle}>Tipo do item</h3>
                        <div className={styles.radioGroup}>
                            <label className={`${styles.radioCard} ${tipoItem === 'pecas' ? styles.radioCardActive : ''}`}>
                                <input
                                    type="radio"
                                    name="tipo_item"
                                    value="pecas"
                                    checked={tipoItem === 'pecas'}
                                    onChange={(e) => setTipoItem(e.target.value)}
                                />
                                <div className={styles.radioContent}>
                                    <span className={styles.radioIcon}>🔧</span>
                                    <span className={styles.radioText}>Peça</span>
                                </div>
                            </label>
                            <label className={`${styles.radioCard} ${tipoItem === 'service' ? styles.radioCardActive : ''}`}>
                                <input
                                    type="radio"
                                    name="tipo_item"
                                    value="service"
                                    checked={tipoItem === 'service'}
                                    onChange={(e) => setTipoItem(e.target.value)}
                                />
                                <div className={styles.radioContent}>
                                    <span className={styles.radioIcon}>⚙️</span>
                                    <span className={styles.radioText}>Serviço</span>
                                </div>
                            </label>
                            <label className={`${styles.radioCard} ${tipoItem === 'adiantamento' ? styles.radioCardActive : ''}`}>
                                <input
                                    type="radio"
                                    name="tipo_item"
                                    value="adiantamento"
                                    checked={tipoItem === 'adiantamento'}
                                    onChange={(e) => setTipoItem(e.target.value)}
                                />
                                <div className={styles.radioContent}>
                                    <span className={styles.radioIcon}>💰</span>
                                    <span className={styles.radioText}>Adiantamento</span>
                                </div>
                            </label>
                        </div>
                    </div>

                    {/* Peças Form */}
                    {tipoItem === 'pecas' && (
                        <div className={styles.formSection}>
                            <div className={styles.formHeader}>
                                <h3>Editar Peça</h3>
                                <a
                                    href="/tabelaPecasAdd"
                                    target="_blank"
                                    className={styles.createButton}
                                >
                                    + Criar Peça
                                </a>
                            </div>

                            <div className={styles.row}>
                                <div className={styles.col12}>
                                    <div className={styles.inputGroup} ref={partInputRef}>
                                        <label>Selecione uma peça</label>
                                        <input
                                            type="text"
                                            value={selectedPart}
                                            onChange={(e) => handlePartSearch(e.target.value)}
                                            onFocus={() => setShowPartSuggestions(true)}
                                            placeholder="Digite para buscar... (ex: motor, embreagem)"
                                            className={styles.searchInput}
                                        />
                                        {showPartSuggestions && filteredParts.length > 0 && (
                                            <div className={styles.suggestions}>
                                                {filteredParts.slice(0, 10).map(part => (
                                                    <div
                                                        key={part.pecaId}
                                                        className={styles.suggestionItem}
                                                        onClick={() => selectPart(part)}
                                                    >
                                                        <span className={styles.suggestionGroup}>{part.grupo}</span>
                                                        <span className={styles.suggestionSeparator}>-</span>
                                                        <span>{part.item}</span>
                                                        <span className={styles.suggestionSeparator}>-</span>
                                                        <span className={styles.suggestionPart}>{part.parte}</span>
                                                    </div>
                                                ))}
                                                {filteredParts.length > 10 && (
                                                    <div className={styles.moreResults}>
                                                        ... e mais {filteredParts.length - 10} resultados
                                                    </div>
                                                )}
                                            </div>
                                        )}
                                        {selectedPartId && (
                                            <div className={styles.selectedBadge}>
                                                ✓ Peça selecionada
                                            </div>
                                        )}
                                    </div>
                                </div>
                            </div>

                            <div className={styles.row}>
                                <div className={styles.col6}>
                                    <div className={styles.inputGroup}>
                                        <label>Código do Produto (opcional)</label>
                                        <input
                                            type="text"
                                            value={scode}
                                            onChange={(e) => setScode(e.target.value)}
                                            placeholder="Ex: ABC-123"
                                        />
                                    </div>
                                </div>
                            </div>

                            <div className={styles.row}>
                                <div className={styles.col3}>
                                    <div className={styles.inputGroup}>
                                        <label>Quantidade</label>
                                        <input
                                            type="number"
                                            min="1"
                                            value={pquantidade}
                                            onChange={(e) => setPquantidade(parseInt(e.target.value) || 1)}
                                        />
                                    </div>
                                </div>
                                <div className={styles.col3}>
                                    <div className={styles.inputGroup}>
                                        <label>Valor Unitário (R$)</label>
                                        <input
                                            type="number"
                                            step="0.01"
                                            min="0"
                                            value={pvalor}
                                            onChange={(e) => setPvalor(parseFloat(e.target.value) || 0)}
                                        />
                                    </div>
                                </div>
                                <div className={styles.col6}>
                                    <button
                                        type="submit"
                                        className={styles.submitButton}
                                        disabled={isSubmitting}
                                    >
                                        {isSubmitting ? 'Salvando...' : 'Salvar Peça'}
                                    </button>
                                </div>
                            </div>
                        </div>
                    )}

                    {/* Serviços Form */}
                    {tipoItem === 'service' && (
                        <div className={styles.formSection}>
                            <div className={styles.formHeader}>
                                <h3>Editar Serviço</h3>
                                <a
                                    href="/tabelaServicosAdd"
                                    target="_blank"
                                    className={styles.createButton}
                                >
                                    + Criar Serviço
                                </a>
                            </div>

                            <div className={styles.row}>
                                <div className={styles.col12}>
                                    <div className={styles.inputGroup} ref={serviceInputRef}>
                                        <label>Selecione um serviço</label>
                                        <input
                                            type="text"
                                            value={selectedService}
                                            onChange={(e) => handleServiceSearch(e.target.value)}
                                            onFocus={() => setShowServiceSuggestions(true)}
                                            placeholder="Digite para buscar... (ex: troca, revisão)"
                                            className={styles.searchInput}
                                        />
                                        {showServiceSuggestions && filteredServices.length > 0 && (
                                            <div className={styles.suggestions}>
                                                {filteredServices.slice(0, 10).map(service => (
                                                    <div
                                                        key={service.servicoId}
                                                        className={styles.suggestionItem}
                                                        onClick={() => selectService(service)}
                                                    >
                                                        <span className={styles.suggestionGroup}>{service.tipo}</span>
                                                        <span className={styles.suggestionSeparator}>-</span>
                                                        <span>{service.item}</span>
                                                    </div>
                                                ))}
                                                {filteredServices.length > 10 && (
                                                    <div className={styles.moreResults}>
                                                        ... e mais {filteredServices.length - 10} resultados
                                                    </div>
                                                )}
                                            </div>
                                        )}
                                        {selectedServiceId && (
                                            <div className={styles.selectedBadge}>
                                                ✓ Serviço selecionado
                                            </div>
                                        )}
                                    </div>
                                </div>
                            </div>

                            <div className={styles.row}>
                                <div className={styles.col3}>
                                    <div className={styles.inputGroup}>
                                        <label>Valor (R$)</label>
                                        <input
                                            type="number"
                                            step="0.01"
                                            min="0"
                                            value={svalor}
                                            onChange={(e) => setSvalor(parseFloat(e.target.value) || 0)}
                                        />
                                    </div>
                                </div>
                                <div className={styles.col3}>
                                    <div className={styles.inputGroup}>
                                        <label>Quantidade</label>
                                        <input
                                            type="number"
                                            min="1"
                                            value={squantidade}
                                            onChange={(e) => setSquantidade(parseInt(e.target.value) || 1)}
                                        />
                                    </div>
                                </div>
                                <div className={styles.col6}>
                                    <button
                                        type="submit"
                                        className={styles.submitButton}
                                        disabled={isSubmitting}
                                    >
                                        {isSubmitting ? 'Salvando...' : 'Salvar Serviço'}
                                    </button>
                                </div>
                            </div>
                        </div>
                    )}

                    {/* Adiantamento Form */}
                    {tipoItem === 'adiantamento' && (
                        <div className={styles.formSection}>
                            <div className={styles.formHeader}>
                                <h3>Editar Adiantamento</h3>
                            </div>

                            <div className={styles.row}>
                                <div className={styles.col9}>
                                    <div className={styles.inputGroup}>
                                        <label>Descrição</label>
                                        <input
                                            type="text"
                                            value={aitem}
                                            onChange={(e) => setAitem(e.target.value)}
                                            placeholder="Ex: Pagamento 16/01/2026"
                                        />
                                    </div>
                                </div>
                                <div className={styles.col3}>
                                    <div className={styles.inputGroup}>
                                        <label>Valor (R$)</label>
                                        <input
                                            type="number"
                                            step="0.01"
                                            min="0"
                                            value={avalor}
                                            onChange={(e) => setAvalor(parseFloat(e.target.value) || 0)}
                                        />
                                    </div>
                                </div>
                            </div>

                            <div className={styles.row}>
                                <div className={styles.col12}>
                                    <button
                                        type="submit"
                                        className={styles.submitButton}
                                        disabled={isSubmitting}
                                    >
                                        {isSubmitting ? 'Salvando...' : 'Salvar Adiantamento'}
                                    </button>
                                </div>
                            </div>
                        </div>
                    )}
                </form>

                {/* Back Button */}
                <div className={styles.backButtonContainer}>
                    <button
                        type="button"
                        onClick={handleGoBack}
                        className={styles.backButton}
                    >
                        ← Voltar para Ordem
                    </button>
                </div>
            </div>
        </section>
    );
}

export default function EditItemPage() {
    return (
        <Suspense fallback={
            <div className={styles.loading}>
                <div className={styles.loadingSpinner}></div>
                <p>Carregando...</p>
            </div>
        }>
            <EditItemContent />
        </Suspense>
    );
}
