'use client';

export default function Footer() {
    return (
        <footer id="footer" style={{
            padding: '6em 0',
            background: '#1c1d26',
            textAlign: 'center',
            borderTop: '1px solid rgba(255,255,255,0.05)'
        }}>
            <div className="container">
                <ul className="icons" style={{
                    display: 'flex',
                    justifyContent: 'center',
                    gap: '2rem',
                    listStyle: 'none',
                    padding: 0,
                    marginBottom: '2rem'
                }}>
                    <li><a href="#" target="_blank" className="icon brands alt fa-twitter" style={{ fontSize: '1.5rem', color: 'rgba(255,255,255,0.5)' }}><span className="label" style={{ display: 'none' }}>Twitter</span></a></li>
                    <li><a href="https://www.youtube.com/channel/UC_rUL6tWuwx-iACNG_uHZVA?sub_confirmation=1" target="_blank" className="icon brands alt fa-youtube" style={{ fontSize: '1.5rem', color: 'rgba(255,255,255,0.5)' }}><span className="label" style={{ display: 'none' }}>Youtube</span></a></li>
                    <li><a href="https://www.instagram.com/xandov/" target="_blank" className="icon brands alt fa-instagram" style={{ fontSize: '1.5rem', color: 'rgba(255,255,255,0.5)' }}><span className="label" style={{ display: 'none' }}>Instagram</span></a></li>
                </ul>
                <ul className="copyright" style={{
                    listStyle: 'none',
                    padding: 0,
                    opacity: 0.5,
                    fontSize: '0.9rem',
                    display: 'flex',
                    flexDirection: 'column',
                    gap: '0.5rem'
                }}>
                    <li><i className="fa-brands fa-whatsapp"></i> <b>WhatsApp:</b> (61) 98128-2136</li>
                    <li><b>Fale com:</b> Robson Alexandre</li>
                    <li style={{ marginTop: '1rem' }}>&copy; Subrider. All rights reserved.</li>
                </ul>
            </div>
        </footer>
    );
}
