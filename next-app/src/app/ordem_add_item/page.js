'use client';

import { useState, useEffect, Suspense } from 'react';
import { useSearchParams, useRouter } from 'next/navigation';
import styles from './page.module.css';

function AddItemContent() {
    const searchParams = useSearchParams();
    const router = useRouter();
    const ordem = searchParams.get('ordem');

    const [tipoItem, setTipoItem] = useState('pecas');
    const [parts, setParts] = useState([]);
    const [services, setServices] = useState([]);

    // Form states
    const [selectedPart, setSelectedPart] = useState(''); // Text value for display
    const [selectedPartId, setSelectedPartId] = useState(''); // ID
    const [scode, setScode] = useState('');
    const [pquantidade, setPquantidade] = useState(1);
    const [pvalor, setPvalor] = useState(0);

    const [selectedService, setSelectedService] = useState('');
    const [selectedServiceId, setSelectedServiceId] = useState('');
    const [squantidade, setSquantidade] = useState(1);
    const [svalor, setSvalor] = useState(0);

    const [aitem, setAitem] = useState('');
    const [avalor, setAvalor] = useState(0);

    // Load parts and services on mount
    useEffect(() => {
        const fetchParts = async () => {
            try {
                const res = await fetch('/api/pecas?limit=1000'); // Fetch all for datalist
                const data = await res.json();
                if (data.items) setParts(data.items);
            } catch (error) {
                console.error('Error fetching parts:', error);
            }
        };

        const fetchServices = async () => {
            try {
                // Assuming an API for services exists, or implementing placeholder
                // const res = await fetch('/api/servicos?limit=1000');
                // const data = await res.json();
                // if (data.items) setServices(data.items);
            } catch (error) {
                console.error('Error fetching services:', error);
            }
        };

        fetchParts();
        fetchServices();

        // Adiantamento default description
        const today = new Date().toLocaleDateString('pt-BR');
        setAitem(`Pagamento ${today}`);
    }, []);

    const handlePartChange = (e) => {
        const value = e.target.value;
        setSelectedPart(value);

        // Find matching part to set ID
        // The value in datalist is "grupo - item - parte"
        // We need to match this. 
        // Or we can try to match loosely. 
        // Ideally we select from the options.

        // Actually, the PHP version uses a datalist where value is the string, and it uses JS to find the data-id.
        // In React, we can check if the value matches any constructed string.

        const match = parts.find(p => `${p.grupo} - ${p.item} - ${p.parte}` === value);
        if (match) {
            setSelectedPartId(match.pecaId);
        } else {
            setSelectedPartId('');
        }
    };

    const handleServiceChange = (e) => {
        const value = e.target.value;
        setSelectedService(value);
        // Logic for services id...
    };

    const handleSubmit = async (e) => {
        e.preventDefault();

        if (!ordem) {
            alert('Ordem ID is missing');
            return;
        }

        const payload = {
            tipo_item: tipoItem,
            ordem
        };

        if (tipoItem === 'pecas') {
            if (!selectedPartId) {
                alert('Selecione uma peça válida');
                return;
            }
            payload.pecaid = selectedPartId;
            payload.pquantidade = pquantidade;
            payload.pvalor = pvalor;
            payload.scode = scode;
        } else if (tipoItem === 'service') {
            // ...
            // For now focusing on Pecas as requested
            alert('Serviço implementation pending');
            return;
        } else if (tipoItem === 'adiantamento') {
            payload.avalor = avalor;
            payload.aitem = aitem;
        }

        try {
            const res = await fetch('/api/ordem_add_item', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload)
            });
            const data = await res.json();

            if (data.success) {
                // Redirect back to ordem service
                // router.push(`/ordemservico?ordem=${ordem}`); // PHP redirects to ordemservico.php
                // Or Next.js route:
                // Assuming structure is /ordemservico/[id] or similar.
                // The PHP redirect: header('Location: ../../ordemservico.php?ordem='. $_GET['ordem']);
                // I'll define the route based on how ordemservico is implemented. 
                // Based on file list: src/app/ordemservico/[...id]/page.js
                // So expected route: /ordemservico/134/2025 (splitted) or similar.

                // Parse ordem "134/2025" -> 134/2025
                router.push(`/ordemservico/${ordem}`);
            } else {
                alert('Error inserting item: ' + data.error);
            }
        } catch (error) {
            console.error('Submit error:', error);
            alert('Error submitting form');
        }
    };

    return (
        <section className={styles.container}>
            <div className={styles.content}>
                <img src="/assets/css/images/logo-branco-crop.png" alt="Logo" style={{ height: '60px', marginBottom: '20px' }} />

                <form onSubmit={handleSubmit}>
                    <div className={styles.radioGroup}>
                        <label className={styles.radioLabel}>
                            <input
                                type="radio"
                                name="tipo_item"
                                value="pecas"
                                checked={tipoItem === 'pecas'}
                                onChange={(e) => setTipoItem(e.target.value)}
                            />
                            <h4>Peça</h4>
                        </label>
                        <label className={styles.radioLabel}>
                            <input
                                type="radio"
                                name="tipo_item"
                                value="service"
                                checked={tipoItem === 'service'}
                                onChange={(e) => setTipoItem(e.target.value)}
                            />
                            <h4>Serviço</h4>
                        </label>
                        <label className={styles.radioLabel}>
                            <input
                                type="radio"
                                name="tipo_item"
                                value="adiantamento"
                                checked={tipoItem === 'adiantamento'}
                                onChange={(e) => setTipoItem(e.target.value)}
                            />
                            <h4>Adiantamento</h4>
                        </label>
                    </div>

                    {tipoItem === 'pecas' && (
                        <div className={styles.formSection}>
                            <div className={styles.row}>
                                <div className={styles.col}>
                                    <h3>Selecione uma peça</h3>
                                    <div className={styles.flexRow}>
                                        <div style={{ flex: 1 }}>
                                            <input
                                                type="text"
                                                list="pecaid_list"
                                                value={selectedPart}
                                                onChange={handlePartChange}
                                                placeholder="Digite para buscar..."
                                                style={{ width: '100%' }}
                                            />
                                            <datalist id="pecaid_list">
                                                {parts.map(part => (
                                                    <option
                                                        key={part.pecaId}
                                                        value={`${part.grupo} - ${part.item} - ${part.parte}`}
                                                    />
                                                ))}
                                            </datalist>
                                        </div>
                                        <div>
                                            <a
                                                href="/tabelaPecasAdd"
                                                target="_blank"
                                                className="button small"
                                                style={{
                                                    display: 'inline-block',
                                                    padding: '0.6em 1em',
                                                    backgroundColor: '#c73650',
                                                    color: 'white',
                                                    borderRadius: '4px',
                                                    fontSize: '0.9em',
                                                    whiteSpace: 'nowrap'
                                                }}
                                            >
                                                + Criar Peça
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div className={styles.row}>
                                <div className={`${styles.col} ${styles.col8}`} style={{ flex: 2 }}>
                                    <div className={styles.inputGroup}>
                                        <label>Código do Produto</label>
                                        <input
                                            type="text"
                                            value={scode}
                                            onChange={(e) => setScode(e.target.value)}
                                        />
                                    </div>
                                </div>
                            </div>

                            <div className={styles.row}>
                                <div className={styles.col}>
                                    <div className={styles.inputGroup}>
                                        <label>Quantidade</label>
                                        <input
                                            type="number"
                                            value={pquantidade}
                                            onChange={(e) => setPquantidade(e.target.value)}
                                        />
                                    </div>
                                </div>
                                <div className={styles.col}>
                                    <div className={styles.inputGroup}>
                                        <label>Valor</label>
                                        <input
                                            type="number"
                                            step=".01"
                                            value={pvalor}
                                            onChange={(e) => setPvalor(e.target.value)}
                                        />
                                    </div>
                                </div>
                                <div className={styles.col} style={{ flex: 2 }}>
                                    <input type="submit" value="Adicionar" className="button primary" style={{ width: '100%', marginTop: '24px' }} />
                                </div>
                            </div>
                        </div>
                    )}

                    {tipoItem === 'service' && (
                        <div className={styles.formSection}>
                            <p>Serviço form placeholder (as focus is on Peça)</p>
                        </div>
                    )}

                    {tipoItem === 'adiantamento' && (
                        <div className={styles.formSection}>
                            <div className={styles.row}>
                                <div className={styles.col} style={{ flex: 3 }}>
                                    <div className={styles.inputGroup}>
                                        <label>Descrição</label>
                                        <input
                                            type="text"
                                            value={aitem}
                                            onChange={(e) => setAitem(e.target.value)}
                                        />
                                    </div>
                                </div>
                                <div className={styles.col}>
                                    <div className={styles.inputGroup}>
                                        <label>Valor</label>
                                        <input
                                            type="number"
                                            step=".01"
                                            value={avalor}
                                            onChange={(e) => setAvalor(e.target.value)}
                                        />
                                    </div>
                                </div>
                            </div>
                            <div className={styles.row}>
                                <div className={styles.col}>
                                    <input type="submit" value="Adicionar" className="button primary" style={{ width: '100%' }} />
                                </div>
                            </div>
                        </div>
                    )}

                </form>
            </div>
        </section>
    );
}

export default function AddItemPage() {
    return (
        <Suspense fallback={<div>Loading...</div>}>
            <AddItemContent />
        </Suspense>
    );
}
