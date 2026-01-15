
import { query } from '@/lib/db';
import { NextResponse } from 'next/server';
import { writeFile } from 'fs/promises';
import path from 'path';

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



export async function POST(request) {
    try {
        const formData = await request.formData();
        const method = formData.get('_method'); // Check for method override if needed, or just specific logic

        // Identify if it is an update based on presence of 'motoId' or explicit method
        // Standard POST is creating new.

        const endereco = formData.get('endereco');
        const ano = formData.get('ano');
        const modelo = formData.get('modelo');
        const marca = formData.get('marca');
        const placa = formData.get('placa');
        const km = formData.get('km');
        const proprietario = formData.get('proprietario');
        const file = formData.get('foto');

        let fotoPath = '';

        if (file && file.size > 0 && file.name !== 'undefined') {
            const buffer = Buffer.from(await file.arrayBuffer());
            // Create unique filename to avoid collisions
            const filename = Date.now() + '_' + file.name.replace(/\s+/g, '_');
            // Resolve upload directory relative to next-app root
            const uploadDir = path.join(process.cwd(), '../upload/moto');

            // Ensure directory exists
            try {
                // We use fs/promises for writeFile but standard fs for sync mkdir ok?
                // Better use standard fs for check
                const fs = require('fs');
                if (!fs.existsSync(uploadDir)) {
                    fs.mkdirSync(uploadDir, { recursive: true });
                }
            } catch (e) {
                // ignore if exists
            }

            await writeFile(path.join(uploadDir, filename), buffer);

            // Store path format consistent with existing PHP logic (relative to root? or just path)
            // PHP logic trims "../../" from "../../upload/moto/" -> "upload/moto/"
            fotoPath = 'upload/moto/' + filename;
        }

        const sql = `INSERT INTO motocicletas (foto, endereco, ano, modelo, marca, placa, KM, proprietario) VALUES (?, ?, ?, ?, ?, ?, ?, ?)`;
        const result = await query(sql, [fotoPath, endereco, ano, modelo, marca, placa, km, proprietario]);

        return NextResponse.json({ success: true, id: result.insertId });
    } catch (error) {
        console.error('POST Error:', error);
        return NextResponse.json({ error: 'Erro ao salvar motocicleta: ' + error.message }, { status: 500 });
    }
}

export async function PUT(request) {
    try {
        const formData = await request.formData();
        const motoId = formData.get('motoId');

        if (!motoId) {
            return NextResponse.json({ error: 'Moto ID required for update' }, { status: 400 });
        }

        // Fetch existing moto to handle optional file update
        // (If no new file, keep old one? Or does client send null?)
        // Usually client sends file only if changed.

        const file = formData.get('foto');
        let fotoPath = null;

        if (file && file.size > 0 && file.name !== 'undefined') {
            const buffer = Buffer.from(await file.arrayBuffer());
            const filename = Date.now() + '_' + file.name.replace(/\s+/g, '_');
            const uploadDir = path.join(process.cwd(), '../upload/moto');

            // Ensure dir
            const fs = require('fs');
            if (!fs.existsSync(uploadDir)) {
                fs.mkdirSync(uploadDir, { recursive: true });
            }

            await writeFile(path.join(uploadDir, filename), buffer);
            fotoPath = 'upload/moto/' + filename;
        }

        // Build Update Query
        // We only update fields that are provided? Or all?
        // Since we are using FormData, we might only support 'foto' update here if that's what's needed.
        // But let's support general update if fields are present.

        // For 'gerenciarfotos', we mostly care about 'foto' (main photo).
        // But let's make it robust.

        const updates = [];
        const params = [];

        if (fotoPath) {
            updates.push('foto = ?');
            params.push(fotoPath);
        }

        // Check other fields
        ['endereco', 'ano', 'modelo', 'marca', 'placa', 'KM', 'proprietario'].forEach(field => {
            if (formData.has(field)) {
                updates.push(`${field} = ?`);
                params.push(formData.get(field));
            }
        });

        if (updates.length === 0) {
            return NextResponse.json({ success: true, message: 'No changes' });
        }

        const sql = `UPDATE motocicletas SET ${updates.join(', ')} WHERE motoId = ?`;
        params.push(motoId);

        await query(sql, params);

        return NextResponse.json({ success: true });

    } catch (error) {
        console.error('PUT Error:', error);
        return NextResponse.json({ error: 'Erro ao atualizar motocicleta: ' + error.message }, { status: 500 });
    }
}
