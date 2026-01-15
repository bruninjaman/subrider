'use client';

import { useState } from 'react';
import { useRouter } from 'next/navigation';
import styles from './page.module.css';

export default function TabelaPecasAdd() {
    const router = useRouter();
    const [grupo, setGrupo] = useState('');
    const [item, setItem] = useState('');
    const [parte, setParte] = useState('');
    const [preview, setPreview] = useState('https://www.abecom.com.br/wp-content/uploads/elementor/thumbs/engrenagem-ou-roda-dentada-ot6vess300oq2nd1xp9ar7ex1cg39ftlquaqn7vsas.jpg');
    const [file, setFile] = useState(null);

    const handleFileChange = (e) => {
        const selectedFile = e.target.files[0];
        if (selectedFile) {
            setFile(selectedFile);
            setPreview(URL.createObjectURL(selectedFile));
        }
    };

    const handleSubmit = async (e) => {
        e.preventDefault();

        const formData = new FormData();
        formData.append('grupo', grupo);
        formData.append('item', item);
        formData.append('parte', parte);
        if (file) {
            formData.append('foto', file);
        }

        try {
            const res = await fetch('/api/pecas/create', {
                method: 'POST',
                body: formData,
            });

            if (res.ok) {
                router.push('/tabelaPecas');
            } else {
                alert('Erro ao criar peça');
            }
        } catch (error) {
            console.error('Error creating peca:', error);
            alert('Erro ao conectar com o servidor');
        }
    };

    return (
        <section className={styles.container}>
            <div className={styles.content}>
                <div className={styles.title}>
                    <img src="/assets/css/images/logo-branco-crop.png" alt="Logo" style={{ height: '40px', marginBottom: '10px' }} />
                    <h2>Adicionar peça</h2>
                </div>

                <form onSubmit={handleSubmit}>
                    <div className={styles.formGrid}>
                        <div className={styles.inputsColumn}>
                            <div className={styles.inputGroup}>
                                <label>Grupo:</label>
                                <input
                                    type="text"
                                    value={grupo}
                                    onChange={(e) => setGrupo(e.target.value)}
                                    required
                                />
                            </div>

                            <div className={styles.inputGroup}>
                                <label>Item:</label>
                                <input
                                    type="text"
                                    value={item}
                                    onChange={(e) => setItem(e.target.value)}
                                    required
                                />
                            </div>

                            <div className={styles.inputGroup}>
                                <label>Parte:</label>
                                <input
                                    type="text"
                                    value={parte}
                                    onChange={(e) => setParte(e.target.value)}
                                    required
                                />
                            </div>
                        </div>

                        <div className={styles.imageColumn}>
                            <div className={styles.uploadCard}>
                                <img src={preview} alt="preview" className={styles.previewImage} />
                                <input
                                    type="file"
                                    name="foto"
                                    onChange={handleFileChange}
                                    className={styles.fileInput}
                                    accept="image/*"
                                />
                                <div className={styles.uploadIcon}>
                                    <i className="fas fa-arrow-circle-up"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <input className={styles.submitButton} type="submit" value="Adicionar" />
                </form>
            </div>
        </section>
    );
}
