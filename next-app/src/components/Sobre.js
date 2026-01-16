'use client';

export default function Sobre() {
    const servicos = [
        { icon: 'fa-trailer', title: 'Trasnsporte/Reboque', desc: 'Transporte guincho para a oficina e para a residência do cliente.' },
        { icon: 'fa-clock-rotate-left', title: 'Manutenção Preventiva', desc: 'Manutenção periódica conforme a tabela do fabricante ou por níveis: básico, intermediário e avançado.' },
        { icon: 'fa-gear', title: 'Manutenção Corretiva', desc: 'Reparo mecânico, elétrico, eletrônico e estético e motocicletas de 1 a 6 cilindros.' },
        { icon: 'fa-broom-ball', title: 'Manutenção Estética', desc: 'Pintura, polimento, recuperação de peças cromadas, soldas, etc.' },
        { icon: 'fa-kit-medical', title: 'Manutenção Emergencial', desc: 'Atendimento de emergência para motocicletas imobilizadas.' },
        { icon: 'fa-coins', title: 'Manutenção Econômica', desc: 'Busca por alternativas seguras de redução de custos de manutenção.' }
    ];

    return (
        <section id="four" style={{ padding: '6em 0', background: '#272833', textAlign: 'center' }}>
            <div className="container">
                <header style={{ marginBottom: '4rem' }}>
                    <h2 style={{ fontSize: '2.5rem' }}>Oferecemos serviço de manutenção em várias marcas de motocicletas</h2>
                    <p style={{ opacity: 0.7 }}>Veja as variadas opções de serviços</p>
                </header>
                <div style={{
                    display: 'grid',
                    gridTemplateColumns: 'repeat(auto-fit, minmax(300px, 1fr))',
                    gap: '3rem'
                }}>
                    {servicos.map((s, i) => (
                        <section key={i} style={{ padding: '2rem', background: 'rgba(255,255,255,0.03)', borderRadius: '15px' }}>
                            <span className={`icon solid alt major fa ${s.icon}`} style={{
                                fontSize: '3rem',
                                color: '#e44c65',
                                marginBottom: '1.5rem',
                                display: 'block'
                            }}></span>
                            <h3 style={{ fontSize: '1.5rem', marginBottom: '1rem' }}>{s.title}</h3>
                            <p style={{ opacity: 0.8, lineHeight: '1.6' }}>{s.desc}</p>
                        </section>
                    ))}
                </div>
                <footer style={{ marginTop: '4rem' }}>
                    <ul className="actions" style={{ display: 'flex', justifyContent: 'center', listStyle: 'none', padding: 0 }}>
                        <li><a href="#footer" className="button">Entrar em contato</a></li>
                    </ul>
                </footer>
            </div>
        </section>
    );
}
