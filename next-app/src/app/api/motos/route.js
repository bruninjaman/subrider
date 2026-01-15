
import { query } from '@/lib/db';
import { NextResponse } from 'next/server';

export async function GET(request) {
    const { searchParams } = new URL(request.url);
    const id = searchParams.get('id');
    const search = searchParams.get('q') || '';
    const orderby = searchParams.get('orderby') || 'motoId';
    const order = searchParams.get('order') || 'DESC';
    const page = parseInt(searchParams.get('page')) || 1;
    const limit = parseInt(searchParams.get('limit')) || 5;
    const offset = (page - 1) * limit;

    try {
        if (id) {
            const sql = `SELECT * FROM motocicletas WHERE motoId = ?`;
            const results = await query(sql, [id]);
            return NextResponse.json(results[0] || null);
        }

        let sql = `SELECT * FROM motocicletas`;
        let countSql = `SELECT COUNT(*) as total FROM motocicletas`;
        const params = [];

        if (search) {
            const whereClause = ` WHERE (
                modelo LIKE ? OR 
                marca LIKE ? OR 
                proprietario LIKE ? OR 
                placa LIKE ? OR 
                ano LIKE ?
            )`;
            sql += whereClause;
            countSql += whereClause;
            const searchPattern = `%${search}%`;
            params.push(searchPattern, searchPattern, searchPattern, searchPattern, searchPattern);
        }

        sql += ` ORDER BY ${orderby} ${order}`;
        sql += ` LIMIT ${limit} OFFSET ${offset}`;

        const results = await query(sql, params);

        // Get total count for pagination
        const countResult = await query(countSql, params);
        const total = countResult[0].total;

        return NextResponse.json({
            items: results,
            total,
            page,
            totalPages: Math.ceil(total / limit)
        });
    } catch (error) {
        console.error('API Error:', error);
        return NextResponse.json({ error: 'Database error' }, { status: 500 });
    }
}

export async function DELETE(request) {
    const { searchParams } = new URL(request.url);
    const motoId = searchParams.get('id');

    if (!motoId) {
        return NextResponse.json({ error: 'Moto ID required' }, { status: 400 });
    }

    try {
        const result = await query('DELETE FROM motocicletas WHERE motoId = ?', [motoId]);

        if (result.affectedRows === 0) {
            return NextResponse.json({ error: 'Motocicleta não encontrada ou já excluída.' }, { status: 404 });
        }

        return NextResponse.json({ success: true });
    } catch (error) {
        console.error('Delete Error:', error);
        return NextResponse.json({ error: 'Erro ao excluir motocicleta.' }, { status: 500 });
    }
}
