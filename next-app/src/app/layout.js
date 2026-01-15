import "./globals.css";
import Header from "@/components/Header";

export const metadata = {
  title: "Subrider - Ordem de Serviço",
  description: "Sistema de gerenciamento de medições e ordens de serviço",
};

export default function RootLayout({ children }) {
  return (
    <html lang="pt-BR">
      <body>
        <Header />
        {children}
      </body>
    </html>
  );
}
