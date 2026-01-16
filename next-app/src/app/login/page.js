'use client';

import { useState, useEffect, Suspense } from 'react';
import { useRouter, useSearchParams } from 'next/navigation';

function LoginForm() {
    const [user, setUser] = useState('');
    const [pass, setPass] = useState('');
    const [error, setError] = useState(null);
    const [attempts, setAttempts] = useState(null);
    const [blockedTime, setBlockedTime] = useState(null);
    const [loading, setLoading] = useState(false);

    const router = useRouter();
    const searchParams = useSearchParams();

    useEffect(() => {
        const errorParam = searchParams.get('error');
        const attemptsParam = searchParams.get('attempts');
        const timeParam = searchParams.get('time');

        if (errorParam) {
            setError(errorParam);
            if (attemptsParam) setAttempts(attemptsParam);
            if (timeParam) setBlockedTime(timeParam);
        }
    }, [searchParams]);

    const handleSubmit = async (e) => {
        if (e) e.preventDefault();
        setLoading(true);
        setError(null);

        try {
            const res = await fetch('/api/login', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ user, pass }),
            });

            const data = await res.json();

            if (res.ok) {
                // Forçar um recarregamento para que o Header atualize o estado de login
                window.location.href = '/';
            } else {
                setError(data.error);
                setAttempts(data.attempts);
                setBlockedTime(data.time);
            }
        } catch (err) {
            setError('internal_error');
        } finally {
            setLoading(false);
        }
    };

    const getErrorMessage = () => {
        switch (error) {
            case 'blocked':
                return (
                    <div className="login-error">
                        Sua conta está temporariamente bloqueada por excesso de tentativas incorretas.<br />
                        <span className="login-counter">Tente novamente em {blockedTime} minutos.</span>
                    </div>
                );
            case 'wrong':
                return (
                    <div className="login-error">
                        Senha incorreta.<br />
                        {attempts !== null && <span className="login-counter">Tentativas restantes: {attempts}</span>}
                    </div>
                );
            case 'nouser':
                return <div className="login-error">Usuário não encontrado.</div>;
            case 'invalid_input':
                return <div className="login-error">Dados de login inválidos.</div>;
            default:
                return null;
        }
    };

    return (
        <div className="content-container">
            <header>
                <h2 style={{ fontSize: '1.5em', marginBottom: '1em' }}>Entre na sua conta</h2>
            </header>

            {getErrorMessage()}

            <form
                onSubmit={handleSubmit}
                id="loginform"
                style={error === 'blocked' ? { pointerEvents: 'none', opacity: 0.6 } : {}}
            >
                <div style={{ marginBottom: '1.5rem' }}>
                    <label htmlFor="user" style={{ display: 'block', textAlign: 'left', marginBottom: '0.5rem', color: '#ffffff', fontSize: '0.9rem' }}>Login:</label>
                    <input
                        type="text"
                        id="user"
                        name="user"
                        value={user}
                        onChange={(e) => setUser(e.target.value)}
                        maxLength={25}
                        required
                        style={{ width: '100%', padding: '0.6rem', background: 'transparent', border: '1px solid rgba(255,255,255,0.3)', borderRadius: '4px', color: 'white' }}
                    />

                    <label htmlFor="pass" style={{ display: 'block', textAlign: 'left', marginBottom: '0.5rem', marginTop: '1rem', color: '#ffffff', fontSize: '0.9rem' }}>Senha:</label>
                    <input
                        type="password"
                        id="pass"
                        name="pass"
                        value={pass}
                        onChange={(e) => setPass(e.target.value)}
                        maxLength={25}
                        required
                        style={{ width: '100%', padding: '0.6rem', background: 'transparent', border: '1px solid rgba(255,255,255,0.3)', borderRadius: '4px', color: 'white' }}
                    />
                </div>
                <div>
                    <button type="submit" className="button primary" disabled={loading} style={{ width: '100%', padding: '0.8rem', background: '#e44c65', border: 'none', borderRadius: '4px', color: 'white', fontWeight: '600', cursor: 'pointer' }}>
                        {loading ? 'Entrando...' : 'Entrar'}
                    </button>
                </div>
            </form>
        </div>
    );
}

export default function LoginPage() {
    return (
        <main style={{ backgroundColor: '#1c1d26', minHeight: '100vh', color: 'rgba(255,255,255,0.75)' }}>
            <style jsx global>{`
                .spotlight {
                    background-attachment: fixed;
                    background-position: center center;
                    background-size: cover;
                    box-shadow: 0 0.25em 0.5em 0 rgba(0, 0, 0, 0.25);
                    height: 100vh;
                    overflow: hidden;
                    position: relative;
                    display: flex;
                    align-items: center;
                    justify-content: flex-end;
                }

                .spotlight:before {
                    background-image: url("/assets/css/images/overlay.png");
                    content: '';
                    display: block;
                    height: 100%;
                    left: 0;
                    position: absolute;
                    top: 0;
                    width: 100%;
                    z-index: 1;
                }

                .spotlight .image.fit.main {
                    position: absolute;
                    top: 0;
                    left: 0;
                    width: 100%;
                    height: 100%;
                    z-index: 0;
                }

                .spotlight .image.fit.main img {
                    width: 100%;
                    height: 100%;
                    object-fit: cover;
                }

                .spotlight .content {
                    background: rgba(23, 24, 32, 0.95);
                    border-left: 0.35em solid #5480f1;
                    padding: 6em 3em;
                    position: relative;
                    z-index: 2;
                    width: 32rem;
                    height: 100%;
                    display: flex;
                    flex-direction: column;
                    justify-content: center;
                }

                .content-container {
                    position: absolute;
                    top: 75%;
                    left: 15%;
                    transform: translate(-50%, -50%);
                    text-align: center;
                    z-index: 3;
                    width: 350px;
                    background: rgba(23, 24, 32, 0.8);
                    padding: 2.5rem;
                    border-radius: 12px;
                    border: 1px solid rgba(255,255,255,0.1);
                    backdrop-filter: blur(10px);
                }

                @media screen and (max-width: 980px) {
                    .spotlight .content {
                        width: 25rem;
                    }
                }

                @media screen and (max-width: 736px) {
                    .spotlight {
                        justify-content: center;
                    }
                    .spotlight .content {
                        display: none;
                    }
                    .content-container {
                        position: absolute;
                        top: 20%;
                        left: 50%;
                        transform: translate(-50%, -50%);
                        width: 90%;
                        margin-top: 0;
                    }
                }

                .login-error {
                    color: #ff3333;
                    margin-bottom: 15px;
                    padding: 8px;
                    border-radius: 5px;
                    background-color: rgba(255, 0, 0, 0.1);
                    border: 1px solid #ff3333;
                    font-weight: bold;
                    font-size: 0.9rem;
                }
                
                .login-counter {
                    font-size: 0.8rem;
                    display: block;
                    margin-top: 5px;
                }

                .button.primary:hover {
                    background: #e76278 !important;
                }

                .button.primary:disabled {
                    opacity: 0.6;
                    cursor: not-allowed;
                }
            `}</style>

            <section className="spotlight style2 right">
                <span className="image fit main">
                    <img src="/assets/css/images/race-moto.gif" alt="" />
                </span>

                <div className="content">
                    <header>
                        <h2 style={{ fontSize: '2.5rem', marginBottom: '1.5rem' }}>Acesso administrativo</h2>
                    </header>
                    <ul style={{ listStyle: 'none', padding: 0, fontSize: '1rem', lineHeight: '1.6' }}>
                        <li style={{ marginBottom: '1.5em' }}>
                            Esta página de login é exclusivamente para administradores. No momento, estamos em processo de desenvolvimento e em breve haverá recursos e possibilidades para que os usuários possam acessar a página. Por enquanto, pedimos compreensão e paciência.
                        </li>
                        <li style={{ marginBottom: '1.5em' }}>
                            Se você precisar de mais informações, não hesite em entrar em contato conosco através do nosso e-mail, WhatsApp ou de alguma de nossas redes sociais. Estamos sempre à disposição para responder a qualquer dúvida ou sugestão.
                        </li>
                        <li>
                            Agradecemos a compreensão e esperamos em breve poder oferecer a vocês a melhor experiência em nossa página.
                        </li>
                    </ul>
                </div>

                <Suspense fallback={<div>Carregando...</div>}>
                    <LoginForm />
                </Suspense>
            </section>
        </main>
    );
}
