'use client';

import Banner from '@/components/Banner';
import Mapa from '@/components/Mapa';
import Youtube from '@/components/Youtube';
import Instagram from '@/components/Instagram';
import Sobre from '@/components/Sobre';
import Footer from '@/components/Footer';

export default function Home() {
  return (
    <main style={{ paddingTop: 0 }}>
      <Banner />
      <Mapa />
      <Youtube />
      <Instagram />
      <Sobre />
      <Footer />
    </main>
  );
}
