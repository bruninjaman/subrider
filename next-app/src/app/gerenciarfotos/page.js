'use client';

import { useState, useEffect, useRef } from 'react';
import { useSearchParams, useRouter } from 'next/navigation';
import Link from 'next/link';

export default function GerenciarFotos() {
    const searchParams = useSearchParams();
    const router = useRouter();
    const motoId = searchParams.get('motoID');

    const [moto, setMoto] = useState(null);
    const [photos, setPhotos] = useState([]);
    const [loading, setLoading] = useState(true);
    const [uploadingMain, setUploadingMain] = useState(false);
    const [uploadingAdditional, setUploadingAdditional] = useState(false);

    // Lightbox state
    const [lightboxOpen, setLightboxOpen] = useState(false);
    const [lightboxImage, setLightboxImage] = useState('');
    const [lightboxCaption, setLightboxCaption] = useState('');

    // Description Modal state
    const [descModalOpen, setDescModalOpen] = useState(false);
    const [editingPhoto, setEditingPhoto] = useState(null);
    const [newDescription, setNewDescription] = useState('');

    useEffect(() => {
        if (!motoId) {
            router.push('/tabelaMotos');
            return;
        }
        fetchMotoData();
        fetchPhotos();
    }, [motoId]);

    const fetchMotoData = async () => {
        try {
            const res = await fetch(`/api/motos?id=${motoId}`);
            if (res.ok) {
                const data = await res.json();
                if (data) {
                    setMoto(data);
                } else {
                    alert('Moto não encontrada');
                    router.push('/tabelaMotos');
                }
            }
        } catch (error) {
            console.error('Error fetching moto:', error);
        }
    };

    const fetchPhotos = async () => {
        try {
            const res = await fetch(`/api/motos/photos?motoId=${motoId}`);
            if (res.ok) {
                const data = await res.json();
                setPhotos(data);
            }
        } catch (error) {
            console.error('Error fetching photos:', error);
        } finally {
            setLoading(false);
        }
    };

    const getImageUrl = (path) => {
        if (!path) return '';
        // Handle paths that might start with ./ or contain ../ 
        // Our API saves as ./upload/... or upload/...
        // We want /upload/...
        return '/' + path.replace(/^\.?\/?/, '').replace('..', '');
    };

    const handleMainPhotoChange = async (e) => {
        const file = e.target.files[0];
        if (!file) return;

        setUploadingMain(true);
        const formData = new FormData();
        formData.append('motoId', motoId);
        formData.append('foto', file);
        // Using PUT for update
        try {
            const res = await fetch('/api/motos', {
                method: 'PUT',
                body: formData
            });
            const data = await res.json();
            if (res.ok && data.success) {
                // Refresh moto data to see new image
                await fetchMotoData();
                alert('Foto principal atualizada com sucesso!');
            } else {
                alert('Erro ao atualizar foto principal: ' + (data.error || 'Erro desconhecido'));
            }
        } catch (error) {
            console.error('Error updating main photo:', error);
            alert('Erro ao atualizar foto principal');
        } finally {
            setUploadingMain(false);
        }
    };

    const handleAdditionalPhotosChange = async (e) => {
        const files = e.target.files;
        if (!files || files.length === 0) return;

        setUploadingAdditional(true);
        const formData = new FormData();
        formData.append('motoId', motoId);
        for (let i = 0; i < files.length; i++) {
            formData.append('fotos', files[i]);
        }

        try {
            const res = await fetch('/api/motos/photos', {
                method: 'POST',
                body: formData
            });
            const data = await res.json();
            if (res.ok && data.success) {
                await fetchPhotos();
                alert(`${data.uploaded.length} foto(s) enviada(s) com sucesso!`);
            } else {
                alert('Erro ao enviar fotos: ' + (data.error || 'Erro desconhecido'));
            }
        } catch (error) {
            console.error('Error uploading photos:', error);
            alert('Erro ao enviar fotos');
        } finally {
            setUploadingAdditional(false);
            // Verify inputs clearance
            e.target.value = '';
        }
    };

    const handleDeletePhoto = async (id) => {
        if (!confirm('Tem certeza que deseja excluir esta foto?')) return;

        try {
            const res = await fetch(`/api/motos/photos?id=${id}`, {
                method: 'DELETE'
            });
            if (res.ok) {
                setPhotos(photos.filter(p => p.id !== id));
            } else {
                alert('Erro ao excluir foto');
            }
        } catch (error) {
            console.error('Error deleting photo:', error);
        }
    };

    const openEditModal = (photo) => {
        setEditingPhoto(photo);
        setNewDescription(photo.descricao || '');
        setDescModalOpen(true);
    };

    const handleSaveDescription = async () => {
        if (!editingPhoto) return;

        try {
            const res = await fetch('/api/motos/photos', {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id: editingPhoto.id, description: newDescription })
            });

            if (res.ok) {
                setPhotos(photos.map(p => p.id === editingPhoto.id ? { ...p, descricao: newDescription } : p));
                setDescModalOpen(false);
                setEditingPhoto(null);
            } else {
                alert('Erro ao salvar descrição');
            }
        } catch (error) {
            console.error('Error saving description:', error);
        }
    };

    const formatKM = (km) => {
        if (!km) return '0';
        return km.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    };

    if (loading) return <div style={{ color: 'white', padding: '50px', textAlign: 'center' }}>Carregando galeria...</div>;
    if (!moto) return <div style={{ color: 'white', padding: '50px', textAlign: 'center' }}>Moto não encontrada.</div>;

    return (
        <div className="container" style={{ padding: '40px 20px', color: '#fff' }}>
            <div style={{
                display: 'flex',
                justifyContent: 'space-between',
                alignItems: 'center',
                marginBottom: '30px',
                borderLeft: '5px solid #e44c65',
                paddingLeft: '20px'
            }}>
                <div>
                    <h1 style={{ margin: 0, fontSize: '2rem', fontWeight: 'bold' }}>Gerenciar Fotos</h1>
                    <p style={{ margin: '5px 0 0', opacity: 0.8 }}>{moto.marca} {moto.modelo} - {moto.ano}</p>
                </div>
                <Link href="/tabelaMotos" className="button secondary">
                    Voltar
                </Link>
            </div>

            <div style={{ display: 'flex', flexWrap: 'wrap', gap: '30px', marginBottom: '40px' }}>
                {/* Moto Info & Main Photo */}
                <div style={{ flex: '1 1 600px', background: 'rgba(255,255,255,0.05)', borderRadius: '15px', padding: '30px', backdropFilter: 'blur(10px)' }}>
                    <div style={{ display: 'flex', gap: '30px', flexWrap: 'wrap' }}>
                        <div style={{ flex: 1 }}>
                            <h3 style={{ borderBottom: '1px solid rgba(255,255,255,0.1)', paddingBottom: '10px', marginBottom: '20px' }}>Informações do Veículo</h3>
                            <p><strong>Placa:</strong> {moto.placa}</p>
                            <p><strong>Proprietário:</strong> {moto.proprietario}</p>
                            <p><strong>KM:</strong> {formatKM(moto.KM)}</p>
                            <p><strong>Cor:</strong> {moto.cor || 'N/A'}</p>
                        </div>
                        <div style={{ flex: '0 0 250px', display: 'flex', flexDirection: 'column', alignItems: 'center' }}>
                            <h4 style={{ marginBottom: '15px' }}>Foto Principal</h4>
                            <div style={{
                                width: '100%',
                                height: '180px',
                                borderRadius: '10px',
                                overflow: 'hidden',
                                position: 'relative',
                                background: '#000',
                                border: '2px solid rgba(255,255,255,0.1)'
                            }}>
                                {moto.foto ? (
                                    <img
                                        src={getImageUrl(moto.foto)}
                                        alt="Foto Principal"
                                        style={{ width: '100%', height: '100%', objectFit: 'cover', cursor: 'pointer' }}
                                        onClick={() => {
                                            setLightboxImage(getImageUrl(moto.foto));
                                            setLightboxCaption('Foto Principal');
                                            setLightboxOpen(true);
                                        }}
                                    />
                                ) : (
                                    <div style={{ display: 'flex', alignItems: 'center', justifyContent: 'center', height: '100%', color: 'rgba(255,255,255,0.3)' }}>
                                        Sem Foto
                                    </div>
                                )}
                                {uploadingMain && (
                                    <div style={{ position: 'absolute', inset: 0, background: 'rgba(0,0,0,0.7)', display: 'flex', alignItems: 'center', justifyContent: 'center' }}>
                                        <i className="fas fa-spinner fa-spin"></i>
                                    </div>
                                )}
                            </div>
                            <label className="button primary small" style={{ marginTop: '15px', cursor: 'pointer', display: 'inline-block' }}>
                                Alterar Foto Principal
                                <input type="file" accept="image/*" onChange={handleMainPhotoChange} style={{ display: 'none' }} />
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            {/* Additional Photos Upload */}
            <div style={{ background: 'rgba(255,255,255,0.03)', borderRadius: '15px', padding: '30px', marginBottom: '40px' }}>
                <h3 style={{ marginBottom: '20px' }}>Adicionar Novas Fotos</h3>
                <div style={{ display: 'flex', alignItems: 'center', gap: '20px' }}>
                    <label className="button primary icon solid fa-images" style={{ cursor: 'pointer', display: 'inline-flex', alignItems: 'center', gap: '10px' }}>
                        <i className="fas fa-camera"></i>
                        Selecionar Fotos
                        <input type="file" multiple accept="image/*" onChange={handleAdditionalPhotosChange} style={{ display: 'none' }} />
                    </label>
                    {uploadingAdditional && <span>Enviando fotos... <i className="fas fa-spinner fa-spin"></i></span>}
                </div>
            </div>

            {/* Gallery */}
            <h3 style={{ marginBottom: '20px', paddingLeft: '10px', borderLeft: '3px solid #e44c65' }}>Galeria de Fotos ({photos.length})</h3>

            {photos.length === 0 ? (
                <div style={{ padding: '40px', textAlign: 'center', background: 'rgba(255,255,255,0.02)', borderRadius: '10px' }}>
                    Nenhuma foto adicional cadastrada.
                </div>
            ) : (
                <div style={{
                    display: 'grid',
                    gridTemplateColumns: 'repeat(auto-fill, minmax(200px, 1fr))',
                    gap: '20px'
                }}>
                    {photos.map(photo => (
                        <div key={photo.id} style={{
                            background: 'rgba(0,0,0,0.3)',
                            borderRadius: '10px',
                            overflow: 'hidden',
                            position: 'relative',
                            boxShadow: '0 4px 15px rgba(0,0,0,0.2)',
                            transition: 'transform 0.2s',
                            border: photo.descricao ? '2px solid #4CAF50' : '1px solid rgba(255,255,255,0.1)'
                        }}
                            className="gallery-item-hover"
                        >
                            <div style={{ height: '200px', overflow: 'hidden', cursor: 'pointer' }} onClick={() => {
                                setLightboxImage(getImageUrl(photo.caminho_foto));
                                setLightboxCaption(photo.descricao);
                                setLightboxOpen(true);
                            }}>
                                <img
                                    src={getImageUrl(photo.caminho_foto)}
                                    alt={photo.descricao || 'Foto Galeria'}
                                    style={{ width: '100%', height: '100%', objectFit: 'cover', transition: 'transform 0.5s' }}
                                    onMouseOver={e => e.target.style.transform = 'scale(1.1)'}
                                    onMouseOut={e => e.target.style.transform = 'scale(1.0)'}
                                />
                            </div>

                            {/* Actions Overlay */}
                            <div style={{
                                position: 'absolute',
                                bottom: 0,
                                left: 0,
                                right: 0,
                                background: 'rgba(0,0,0,0.8)',
                                padding: '10px',
                                display: 'flex',
                                justifyContent: 'space-around',
                                backdropFilter: 'blur(5px)'
                            }}>
                                <button
                                    onClick={() => openEditModal(photo)}
                                    title="Editar descrição"
                                    style={{ background: 'none', border: 'none', color: '#fff', cursor: 'pointer', fontSize: '1.1rem' }}
                                >
                                    ✏️
                                </button>
                                <button
                                    onClick={() => handleDeletePhoto(photo.id)}
                                    title="Excluir foto"
                                    style={{ background: 'none', border: 'none', color: '#ff4444', cursor: 'pointer', fontSize: '1.1rem' }}
                                >
                                    🗑️
                                </button>
                            </div>
                            {photo.descricao && (
                                <div style={{
                                    position: 'absolute',
                                    top: 0,
                                    left: 0,
                                    right: 0,
                                    background: 'rgba(0,0,0,0.6)',
                                    color: '#fff',
                                    padding: '5px 10px',
                                    fontSize: '0.8rem',
                                    whiteSpace: 'nowrap',
                                    overflow: 'hidden',
                                    textOverflow: 'ellipsis'
                                }}>
                                    {photo.descricao}
                                </div>
                            )}
                        </div>
                    ))}
                </div>
            )}

            {/* Lightbox Modal */}
            {lightboxOpen && (
                <div style={{
                    position: 'fixed', inset: 0, background: 'rgba(0,0,0,0.95)', zIndex: 2000,
                    display: 'flex', flexDirection: 'column', alignItems: 'center', justifyContent: 'center'
                }} onClick={() => setLightboxOpen(false)}>
                    <button
                        onClick={() => setLightboxOpen(false)}
                        style={{ position: 'absolute', top: '20px', right: '20px', background: 'rgba(255,255,255,0.1)', border: 'none', color: '#fff', fontSize: '2rem', cursor: 'pointer', borderRadius: '50%', width: '50px', height: '50px' }}
                    >
                        &times;
                    </button>
                    <img src={lightboxImage} alt="Zoom" style={{ maxWidth: '90%', maxHeight: '80vh', borderRadius: '5px', boxShadow: '0 0 50px rgba(0,0,0,0.5)' }} onClick={e => e.stopPropagation()} />
                    {lightboxCaption && <div style={{ color: '#fff', marginTop: '20px', fontSize: '1.2rem', background: 'rgba(0,0,0,0.5)', padding: '10px 20px', borderRadius: '20px' }}>{lightboxCaption}</div>}
                </div>
            )}

            {/* Edit Description Modal */}
            {descModalOpen && (
                <div style={{
                    position: 'fixed', inset: 0, background: 'rgba(0,0,0,0.8)', zIndex: 1500,
                    display: 'flex', alignItems: 'center', justifyContent: 'center'
                }} onClick={() => setDescModalOpen(false)}>
                    <div style={{
                        background: '#2a2a2a',
                        padding: '30px',
                        borderRadius: '15px',
                        width: '90%',
                        maxWidth: '500px',
                        boxShadow: '0 10px 30px rgba(0,0,0,0.5)',
                        border: '1px solid rgba(255,255,255,0.1)'
                    }} onClick={e => e.stopPropagation()}>
                        <h3 style={{ marginTop: 0, color: '#e44c65' }}>Editar Descrição</h3>
                        <textarea
                            value={newDescription}
                            onChange={e => setNewDescription(e.target.value)}
                            placeholder="Digite uma descrição para a foto..."
                            rows="4"
                            style={{
                                width: '100%',
                                padding: '15px',
                                borderRadius: '10px',
                                border: '1px solid #444',
                                background: '#1a1a1a',
                                color: '#fff',
                                margin: '20px 0',
                                resize: 'vertical'
                            }}
                        />
                        <div style={{ display: 'flex', justifyContent: 'flex-end', gap: '15px' }}>
                            <button className="button secondary" onClick={() => setDescModalOpen(false)}>Cancelar</button>
                            <button className="button primary" onClick={handleSaveDescription}>Salvar</button>
                        </div>
                    </div>
                </div>
            )}
        </div>
    );
}
