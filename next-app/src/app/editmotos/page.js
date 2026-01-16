'use client';

import { useState, useEffect, Suspense } from 'react';
import { useRouter, useSearchParams } from 'next/navigation';
import Link from 'next/link';

function EditMotosContent() {
    const router = useRouter();
    const searchParams = useSearchParams();
    const motoID = searchParams.get('motoID');

    const [loading, setLoading] = useState(true);
    const [saving, setSaving] = useState(false);
    const [preview, setPreview] = useState(null);
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

    const [extraPhotos, setExtraPhotos] = useState([]);
    const [newExtraPhotos, setNewExtraPhotos] = useState([]);
    const [extraPhotoDesc, setExtraPhotoDesc] = useState('');

    useEffect(() => {
        if (!motoID) {
            router.push('/tabelaMotos');
            return;
        }
        fetchMotoData();
        fetchExtraPhotos();
    }, [motoID]);

    const fetchMotoData = async () => {
        try {
            const res = await fetch(`/api/motos?id=${motoID}`);
            if (res.ok) {
                const data = await res.json();
                if (data) {
                    setFormData({
                        endereco: data.endereco || '',
                        ano: data.ano || '',
                        modelo: data.modelo || '',
                        marca: data.marca || '',
                        proprietario: data.proprietario || '',
                        placa: data.placa || '',
                        km: data.KM || '',
                        foto: null
                    });
                    if (data.foto) {
                        setPreview(getImageUrl(data.foto));
                    }
                } else {
                    alert('Motocicleta não encontrada');
                    router.push('/tabelaMotos');
                }
            }
        } catch (error) {
            console.error('Error:', error);
        } finally {
            setLoading(false);
        }
    };

    const fetchExtraPhotos = async () => {
        try {
            const res = await fetch(`/api/motos/photos?motoId=${motoID}`);
            if (res.ok) {
                const data = await res.json();
                setExtraPhotos(data);
            }
        } catch (error) {
            console.error('Error fetching extra photos:', error);
        }
    };

    const getImageUrl = (path) => {
        if (!path) return '';
        return '/' + path.replace(/^\.?\/?/, '').replace('..', '');
    };

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

    const handleExtraPhotosChange = (e) => {
        const files = Array.from(e.target.files);
        setNewExtraPhotos(files);
    };

    const handleDeleteExtraPhoto = async (id) => {
        if (!confirm('Deseja realmente excluir esta foto?')) return;

        try {
            const res = await fetch(`/api/motos/photos?id=${id}`, {
                method: 'DELETE'
            });
            if (res.ok) {
                setExtraPhotos(prev => prev.filter(p => p.id !== id));
            } else {
                alert('Erro ao excluir foto');
            }
        } catch (error) {
            console.error('Error:', error);
        }
    };

    const handleSubmit = async (e) => {
        e.preventDefault();
        setSaving(true);

        // 1. Update main info
        const data = new FormData();
        data.append('motoId', motoID);
        data.append('endereco', formData.endereco);
        data.append('ano', formData.ano);
        data.append('modelo', formData.modelo);
        data.append('marca', formData.marca);
        data.append('proprietario', formData.proprietario);
        data.append('placa', formData.placa);
        data.append('KM', formData.km);
        if (formData.foto) {
            data.append('foto', formData.foto);
        }

        try {
            const res = await fetch('/api/motos', {
                method: 'PUT',
                body: data
            });

            if (!res.ok) {
                throw new Error('Erro ao atualizar dados principais');
            }

            // 2. Upload extra photos if any
            if (newExtraPhotos.length > 0) {
                const extraData = new FormData();
                extraData.append('motoId', motoID);
                extraData.append('descricao', extraPhotoDesc);
                newExtraPhotos.forEach(file => {
                    extraData.append('fotos', file);
                });
                // Note: The API currently doesn't take description in the same POST call 
                // but we can add it or just ignore it for now as per current API capability.
                // The PHP version had a description field for ALL new photos.

                const extraRes = await fetch('/api/motos/photos', {
                    method: 'POST',
                    body: extraData
                });

                if (!extraRes.ok) {
                    alert('Aviso: Os dados foram salvos mas houve um erro ao enviar as fotos extras.');
                }
            }

            router.push('/tabelaMotos');
        } catch (error) {
            console.error('Error:', error);
            alert('Erro ao salvar as alterações');
        } finally {
            setSaving(false);
        }
    };

    if (loading) {
        return <div style={{ color: 'white', padding: '100px', textAlign: 'center' }}>Carregando dados...</div>;
    }

    return (
        <div className="container" style={{ padding: '60px 0', maxWidth: '800px' }}>
            <div style={{
                marginBottom: '40px',
                borderLeft: '5px solid #e44c65',
                paddingLeft: '20px'
            }}>
                <h1 style={{ margin: 0, fontSize: '2.5rem' }}>Editar informações do Veículo</h1>
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
                {/* Main Photo */}
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
                        {preview ? (
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
                        ) : (
                            <div style={{ color: '#555' }}>Sem foto</div>
                        )}
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
                    <p style={{ color: '#aaa', marginTop: '10px', fontSize: '0.9rem' }}>Alterar foto principal</p>
                </div>

                {/* Info Fields */}
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

                <hr style={{ border: 'none', borderTop: '1px solid rgba(255,255,255,0.1)', margin: '40px 0' }} />

                {/* Extra Photos Section */}
                <div style={{ marginBottom: '40px' }}>
                    <h3 style={{ marginBottom: '10px' }}>Fotos Extras</h3>
                    <p style={{ color: '#aaa', fontSize: '0.9rem', marginBottom: '20px' }}>Adicione fotos extras da motocicleta (peças, detalhes, etc)</p>

                    {extraPhotos.length > 0 && (
                        <div style={{ marginBottom: '30px' }}>
                            <h4 style={{ fontSize: '1rem', color: '#ccc', marginBottom: '15px' }}>Fotos existentes:</h4>
                            <div style={{ display: 'grid', gridTemplateColumns: 'repeat(auto-fill, minmax(150px, 1fr))', gap: '15px' }}>
                                {extraPhotos.map(photo => (
                                    <div key={photo.id} style={{ position: 'relative', borderRadius: '8px', overflow: 'hidden', height: '120px', background: 'rgba(0,0,0,0.2)' }}>
                                        <img
                                            src={getImageUrl(photo.caminho_foto)}
                                            alt="Extra"
                                            style={{ width: '100%', height: '100%', objectFit: 'cover' }}
                                        />
                                        <button
                                            type="button"
                                            onClick={() => handleDeleteExtraPhoto(photo.id)}
                                            style={{
                                                position: 'absolute',
                                                top: '5px',
                                                right: '5px',
                                                background: 'rgba(228, 76, 101, 0.8)',
                                                border: 'none',
                                                color: 'white',
                                                borderRadius: '4px',
                                                padding: '2px 8px',
                                                fontSize: '0.8rem',
                                                cursor: 'pointer'
                                            }}
                                        >
                                            Excluir
                                        </button>
                                        {photo.descricao && (
                                            <div style={{
                                                position: 'absolute',
                                                bottom: 0,
                                                left: 0,
                                                right: 0,
                                                background: 'rgba(0,0,0,0.5)',
                                                color: 'white',
                                                padding: '2px 5px',
                                                fontSize: '0.7rem'
                                            }}>
                                                {photo.descricao}
                                            </div>
                                        )}
                                    </div>
                                ))}
                            </div>
                        </div>
                    )}

                    <div style={{ background: 'rgba(255,255,255,0.02)', padding: '20px', borderRadius: '10px' }}>
                        <h4 style={{ fontSize: '1rem', color: '#ccc', marginBottom: '15px' }}>Adicionar novas fotos:</h4>
                        <input
                            type="file"
                            multiple
                            onChange={handleExtraPhotosChange}
                            style={{ marginBottom: '15px', color: '#aaa' }}
                        />
                        <div style={{ marginTop: '10px' }}>
                            <label style={{ display: 'block', marginBottom: '8px', color: '#ccc', fontSize: '0.9rem' }}>Descrição das fotos:</label>
                            <input
                                type="text"
                                placeholder="Ex: Peça danificada, vista lateral, etc"
                                value={extraPhotoDesc}
                                onChange={(e) => setExtraPhotoDesc(e.target.value)}
                                style={{
                                    width: '100%',
                                    padding: '10px 15px',
                                    background: 'rgba(255,255,255,0.05)',
                                    border: '1px solid rgba(255,255,255,0.1)',
                                    borderRadius: '8px',
                                    color: 'white',
                                    fontSize: '0.9rem'
                                }}
                            />
                        </div>
                    </div>
                </div>

                <div style={{ textAlign: 'center' }}>
                    <button
                        type="submit"
                        disabled={saving}
                        className="button"
                        style={{
                            padding: '12px 50px',
                            fontSize: '1.2rem',
                            opacity: saving ? 0.7 : 1,
                            cursor: saving ? 'wait' : 'pointer'
                        }}
                    >
                        {saving ? 'Salvando...' : 'Editar Moto'}
                    </button>
                    {saving && <p style={{ marginTop: '10px', color: '#ccc' }}>Aguarde enquanto salvamos as alterações...</p>}
                </div>
            </form>
        </div>
    );
}

export default function EditMotos() {
    return (
        <Suspense fallback={<div style={{ color: 'white', padding: '100px', textAlign: 'center' }}>Carregando...</div>}>
            <EditMotosContent />
        </Suspense>
    );
}
