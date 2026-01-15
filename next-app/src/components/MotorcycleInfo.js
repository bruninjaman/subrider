export default function MotorcycleInfo({ motorcycle }) {
    if (!motorcycle) return null;

    return (
        <section id="motoinfo" style={{ marginTop: '60px' }}>
            <div className="report-container" style={{ maxWidth: '1000px', background: 'transparent', padding: 0 }}>
                <div className="report-header" style={{ marginBottom: '30px', borderBottom: 'none' }}>
                    <h2 style={{ fontSize: '2rem', display: 'flex', alignItems: 'center', gap: '15px' }}>
                        <span style={{ color: '#e44c65' }}>🏍️</span>
                        Especificações da Motocicleta
                    </h2>
                </div>

                <div style={{
                    display: 'grid',
                    gridTemplateColumns: 'repeat(auto-fit, minmax(350px, 1fr))',
                    gap: '30px',
                    background: 'rgba(255,255,255,0.02)',
                    padding: '30px',
                    borderRadius: '20px',
                    border: '1px solid rgba(255,255,255,0.05)',
                    backdropFilter: 'blur(5px)'
                }}>
                    <div style={{ textAlign: 'center', display: 'flex', alignItems: 'center', justifyContent: 'center' }}>
                        {motorcycle.foto && (
                            <img src={motorcycle.foto.startsWith('http') ? motorcycle.foto : `/${motorcycle.foto}`} alt="Moto" style={{
                                maxWidth: '100%',
                                maxHeight: '400px',
                                borderRadius: '15px',
                                boxShadow: '0 15px 40px rgba(0,0,0,0.6)',
                                border: '2px solid rgba(255,255,255,0.1)'
                            }} />
                        )}
                    </div>

                    <div style={{ display: 'flex', flexDirection: 'column', justifyContent: 'center' }}>
                        <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: '20px' }}>
                            <SpecCard label="PROPRIETÁRIO" value={motorcycle.proprietario || 'NÃO INFORMADO'} />
                            <SpecCard label="MARCA" value={motorcycle.marca} />
                            <SpecCard label="MODELO" value={motorcycle.modelo} />
                            <SpecCard label="PLACA" value={motorcycle.placa} />
                            <SpecCard label="ANO" value={motorcycle.ano} />
                            <SpecCard label="KM ATUAL" value={`${motorcycle.km} KM`} />
                        </div>
                    </div>
                </div>
            </div>
        </section>
    );
}

function SpecCard({ label, value }) {
    return (
        <div style={{
            padding: '20px',
            background: 'rgba(255,255,255,0.03)',
            borderRadius: '12px',
            borderLeft: '3px solid #e44c65'
        }}>
            <span style={{ display: 'block', fontSize: '0.75rem', color: 'rgba(255,255,255,0.4)', letterSpacing: '1px', marginBottom: '5px' }}>{label}</span>
            <span style={{ display: 'block', fontSize: '1.1rem', fontWeight: 'bold', color: 'white' }}>{value}</span>
        </div>
    );
}
