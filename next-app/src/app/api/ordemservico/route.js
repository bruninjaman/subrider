import { query } from '@/lib/db';
import { NextResponse } from 'next/server';

export async function GET(request) {
    const { searchParams } = new URL(request.url);
    const search = searchParams.get('q') || '';
    const orderby = searchParams.get('orderby') || 'ordem_servicos.servID';
    const order = searchParams.get('order') || 'DESC';

    try {
        let sql = `
      SELECT ordem_servicos.*, motocicletas.modelo, motocicletas.marca, motocicletas.ano, motocicletas.foto, motocicletas.proprietario
      FROM ordem_servicos
      LEFT JOIN motocicletas ON motocicletas.motoId = ordem_servicos.motoID
    `;

        const params = [];
        if (search) {
            sql += `
        WHERE (
          motocicletas.modelo LIKE ? OR 
          motocicletas.marca LIKE ? OR 
          motocicletas.proprietario LIKE ? OR 
          ordem_servicos.proprietario_ordem LIKE ? OR 
          ordem_servicos.Codigo LIKE ?
        )
      `;
            const searchPattern = `%${search}%`;
            params.push(searchPattern, searchPattern, searchPattern, searchPattern, searchPattern);
        }

        sql += ` ORDER BY ${orderby} ${order}`;

        // Default limit for simplicity
        sql += ` LIMIT 50`;

        const results = await query(sql, params);
        return NextResponse.json(results);
    } catch (error) {
        console.error('API Error:', error);
        return NextResponse.json({ error: 'Database error' }, { status: 500 });
    }
}
