import { query } from '@/lib/db';
import { NextResponse } from 'next/server';

export async function POST(request, { params }) {
    const { id } = params;
    const body = await request.json();
    const { table, measurements } = body;

    if (!table || !measurements) {
        return NextResponse.json({ error: 'Faltam dados da tabela ou medições' }, { status: 400 });
    }

    try {
        // Check if record exists
        const existing = await query(`SELECT * FROM ${table} WHERE ordem = ? AND is_reference = 0`, [id]);

        const measurementsJson = JSON.stringify(measurements);

        if (existing.length > 0) {
            // Update
            await query(`UPDATE ${table} SET medicoes = ? WHERE ordem = ? AND is_reference = 0`, [measurementsJson, id]);
        } else {
            // Insert
            await query(`INSERT INTO ${table} (ordem, medicoes, is_reference) VALUES (?, ?, 0)`, [id, measurementsJson]);
        }

        return NextResponse.json({ success: true });
    } catch (error) {
        console.error('Update Error:', error);
        return NextResponse.json({ error: 'Erro ao salvar no banco de dados' }, { status: 500 });
    }
}
