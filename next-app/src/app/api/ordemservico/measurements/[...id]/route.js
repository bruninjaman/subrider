import { query } from '@/lib/db';
import { NextResponse } from 'next/server';

export async function POST(request, { params }) {
    const rawParams = await params;
    const id = Array.isArray(rawParams.id) ? rawParams.id.join('/') : rawParams.id;
    const body = await request.json();
    const { table, measurements, is_reference = 0 } = body;

    if (!table || !measurements) {
        return NextResponse.json({ error: 'Faltam dados da tabela ou medições' }, { status: 400 });
    }

    try {
        // Check if record exists
        const existing = await query(`SELECT * FROM ${table} WHERE ordem = ? AND is_reference = ?`, [id, is_reference]);

        const measurementsJson = typeof measurements === 'string' ? measurements : JSON.stringify(measurements);

        if (existing.length > 0) {
            // Update
            await query(`UPDATE ${table} SET medicoes = ? WHERE ordem = ? AND is_reference = ?`, [measurementsJson, id, is_reference]);
        } else {
            // Insert
            await query(`INSERT INTO ${table} (ordem, medicoes, is_reference) VALUES (?, ?, ?)`, [id, measurementsJson, is_reference]);
        }

        return NextResponse.json({ success: true });
    } catch (error) {
        console.error('Update Error:', error);
        return NextResponse.json({ error: 'Erro ao salvar no banco de dados' }, { status: 500 });
    }
}
