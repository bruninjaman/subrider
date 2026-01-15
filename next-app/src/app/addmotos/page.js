'use client';

import { useState } from 'react';
import { useRouter } from 'next/navigation';
import Link from 'next/link';

export default function AddMotos() {
    const router = useRouter();
    const [loading, setLoading] = useState(false);
    const [preview, setPreview] = useState('https://www.honda.com.br/motos/sites/hda/files/styles/product_860x550/public/2022-08/CG%20Start%20-%20Azul%20Perolizado.png?itok=RG1S7qe5');
    const [formData, setFormData] = useState({
        endereco: '',
        ano: '',
        modelo: '',
        marca: '',
        proprietario: '',
        placa: '',
        km: '',
        foto: null
    });

    const handleChange = (e) => {
        const { name, value } = e.target;
        setFormData(prev => ({ ...prev, [name]: value }));
    };

    const handleFileChange = (e) => {
        const file = e.target.files[0];
        if (file) {
            setFormData(prev => ({ ...prev, foto: file }));
            setPreview(URL.createObjectURL(file));
        }
    };

    const handleSubmit = async (e) => {
        e.preventDefault();
        setLoading(true);

        const data = new FormData();
        data.append('endereco', formData.endereco);
        data.append('ano', formData.ano);
        data.append('modelo', formData.modelo);
        data.append('marca', formData.marca);
        data.append('proprietario', formData.proprietario);
        data.append('placa', formData.placa);
        data.append('km', formData.km);
        if (formData.foto) {
            data.append('foto', formData.foto);
        }

        try {
            const res = await fetch('/api/motos', {
                method: 'POST',
                body: data
            });

            if (res.ok) {
                router.push('/tabelaMotos');
            } else {
                const errorData = await res.json();
                alert('Erro ao adicionar motocicleta: ' + (errorData.error || 'Erro desconhecido'));
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Erro ao adicionar motocicleta');
        } finally {
            setLoading(false);
        }
    };

    return (
        <div className="container" style={{ padding: '60px 0', maxWidth: '800px' }}>
            <div style={{
                marginBottom: '40px',
                borderLeft: '5px solid #e44c65',
                paddingLeft: '20px'
            }}>
                <h1 style={{ margin: 0, fontSize: '2.5rem' }}>Adicionar Novo Veículo</h1>
                <Link href="/tabelaMotos" style={{ color: '#aaa', textDecoration: 'none', marginTop: '10px', display: 'inline-block' }}>
                    &larr; Voltar para a tabela
                </Link>
            </div>

            <form onSubmit={handleSubmit} style={{
                background: 'rgba(255,255,255,0.03)',
                padding: '40px',
                borderRadius: '15px',
                backdropFilter: 'blur(5px)',
                boxShadow: '0 10px 30px rgba(0,0,0,0.2)'
            }}>
                {/* Photo Upload */}
                <div style={{ marginBottom: '40px', textAlign: 'center' }}>
                    <div style={{
                        width: '100%',
                        maxWidth: '400px',
                        height: '250px',
                        margin: '0 auto',
                        position: 'relative',
                        borderRadius: '15px',
                        overflow: 'hidden',
                        border: '2px dashed rgba(255,255,255,0.1)',
                        background: 'rgba(0,0,0,0.2)',
                        display: 'flex',
                        alignItems: 'center',
                        justifyContent: 'center'
                    }}>
                        <img
                            src={preview}
                            alt="Preview"
                            style={{
                                width: '100%',
                                height: '100%',
                                objectFit: 'contain',
                                position: 'absolute'
                            }}
                        />
                        <div style={{
                            position: 'absolute',
                            bottom: '10px',
                            right: '10px',
                            background: '#e44c65',
                            borderRadius: '50%',
                            width: '40px',
                            height: '40px',
                            display: 'flex',
                            alignItems: 'center',
                            justifyContent: 'center',
                            cursor: 'pointer',
                            boxShadow: '0 4px 10px rgba(0,0,0,0.3)'
                        }} onClick={() => document.getElementById('fotoInput').click()}>
                            <span style={{ fontSize: '24px', fontWeight: 'bold', color: 'white', marginTop: '-4px' }}>+</span>
                        </div>
                        <input
                            id="fotoInput"
                            type="file"
                            name="foto"
                            accept="image/*"
                            onChange={handleFileChange}
                            style={{ display: 'none' }}
                        />
                    </div>
                    <p style={{ color: '#aaa', marginTop: '10px', fontSize: '0.9rem' }}>Clique no + para adicionar uma foto</p>
                </div>

                <div className="row">
                    <div className="col-12" style={{ marginBottom: '20px' }}>
                        <label style={{ display: 'block', marginBottom: '8px', color: '#ccc' }}>Endereço</label>
                        <input
                            type="text"
                            name="endereco"
                            required
                            value={formData.endereco}
                            onChange={handleChange}
                            style={{
                                width: '100%',
                                padding: '12px 15px',
                                background: 'rgba(255,255,255,0.05)',
                                border: '1px solid rgba(255,255,255,0.1)',
                                borderRadius: '8px',
                                color: 'white',
                                fontSize: '1rem'
                            }}
                        />
                    </div>
                </div>

                <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr 1fr', gap: '20px', marginBottom: '20px' }}>
                    <div>
                        <label style={{ display: 'block', marginBottom: '8px', color: '#ccc' }}>Ano</label>
                        <input
                            type="text"
                            name="ano"
                            required
                            minLength={4}
                            maxLength={4}
                            value={formData.ano}
                            onChange={handleChange}
                            style={{
                                width: '100%',
                                padding: '12px 15px',
                                background: 'rgba(255,255,255,0.05)',
                                border: '1px solid rgba(255,255,255,0.1)',
                                borderRadius: '8px',
                                color: 'white',
                                fontSize: '1rem'
                            }}
                        />
                    </div>
                    <div style={{ gridColumn: 'span 2' }}>
                        <label style={{ display: 'block', marginBottom: '8px', color: '#ccc' }}>Modelo</label>
                        <input
                            type="text"
                            name="modelo"
                            required
                            value={formData.modelo}
                            onChange={handleChange}
                            style={{
                                width: '100%',
                                padding: '12px 15px',
                                background: 'rgba(255,255,255,0.05)',
                                border: '1px solid rgba(255,255,255,0.1)',
                                borderRadius: '8px',
                                color: 'white',
                                fontSize: '1rem'
                            }}
                        />
                    </div>
                </div>

                <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: '20px', marginBottom: '20px' }}>
                    <div>
                        <label style={{ display: 'block', marginBottom: '8px', color: '#ccc' }}>Marca</label>
                        <input
                            type="text"
                            name="marca"
                            required
                            value={formData.marca}
                            onChange={handleChange}
                            style={{
                                width: '100%',
                                padding: '12px 15px',
                                background: 'rgba(255,255,255,0.05)',
                                border: '1px solid rgba(255,255,255,0.1)',
                                borderRadius: '8px',
                                color: 'white',
                                fontSize: '1rem'
                            }}
                        />
                    </div>
                    <div>
                        <label style={{ display: 'block', marginBottom: '8px', color: '#ccc' }}>KM</label>
                        <input
                            type="number"
                            name="km"
                            required
                            value={formData.km}
                            onChange={handleChange}
                            style={{
                                width: '100%',
                                padding: '12px 15px',
                                background: 'rgba(255,255,255,0.05)',
                                border: '1px solid rgba(255,255,255,0.1)',
                                borderRadius: '8px',
                                color: 'white',
                                fontSize: '1rem'
                            }}
                        />
                    </div>
                </div>

                <div style={{ display: 'grid', gridTemplateColumns: '2fr 1fr', gap: '20px', marginBottom: '40px' }}>
                    <div>
                        <label style={{ display: 'block', marginBottom: '8px', color: '#ccc' }}>Proprietário</label>
                        <input
                            type="text"
                            name="proprietario"
                            required
                            value={formData.proprietario}
                            onChange={handleChange}
                            style={{
                                width: '100%',
                                padding: '12px 15px',
                                background: 'rgba(255,255,255,0.05)',
                                border: '1px solid rgba(255,255,255,0.1)',
                                borderRadius: '8px',
                                color: 'white',
                                fontSize: '1rem'
                            }}
                        />
                    </div>
                    <div>
                        <label style={{ display: 'block', marginBottom: '8px', color: '#ccc' }}>Placa</label>
                        <input
                            type="text"
                            name="placa"
                            required
                            value={formData.placa}
                            onChange={handleChange}
                            style={{
                                width: '100%',
                                padding: '12px 15px',
                                background: 'rgba(255,255,255,0.05)',
                                border: '1px solid rgba(255,255,255,0.1)',
                                borderRadius: '8px',
                                color: 'white',
                                fontSize: '1rem'
                            }}
                        />
                    </div>
                </div>

                <div style={{ textAlign: 'center' }}>
                    <button
                        type="submit"
                        disabled={loading}
                        className="button"
                        style={{
                            padding: '12px 50px',
                            fontSize: '1.2rem',
                            opacity: loading ? 0.7 : 1,
                            cursor: loading ? 'wait' : 'pointer'
                        }}
                    >
                        {loading ? 'Salvando...' : 'Salvar Veículo'}
                    </button>
                    {loading && <p style={{ marginTop: '10px', color: '#ccc' }}>Aguarde enquanto salvamos as informações...</p>}
                </div>
            </form>
        </div>
    );
}
