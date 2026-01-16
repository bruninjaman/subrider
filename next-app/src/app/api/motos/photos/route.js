
import { query } from '@/lib/db';
import { NextResponse } from 'next/server';
import { writeFile, unlink } from 'fs/promises';
import path from 'path';
import fs from 'fs';

export async function GET(request) {
    const { searchParams } = new URL(request.url);
    const motoId = searchParams.get('motoId');

    if (!motoId) {
        return NextResponse.json({ error: 'Moto ID is required' }, { status: 400 });
    }

    try {
        const sql = `SELECT * FROM moto_fotos WHERE motoId = ? ORDER BY id DESC`;
        const results = await query(sql, [motoId]);
        return NextResponse.json(results);
    } catch (error) {
        console.error('API Error:', error);
        return NextResponse.json({ error: 'Database error' }, { status: 500 });
    }
}

export async function POST(request) {
    try {
        const formData = await request.formData();
        const motoId = formData.get('motoId');
        const files = formData.getAll('fotos');

        if (!motoId) {
            return NextResponse.json({ error: 'Moto ID is required' }, { status: 400 });
        }

        if (!files || files.length === 0) {
            return NextResponse.json({ error: 'No files uploaded' }, { status: 400 });
        }

        const uploadDir = path.join(process.cwd(), 'public/upload/moto');

        // Ensure directory exists
        if (!fs.existsSync(uploadDir)) {
            fs.mkdirSync(uploadDir, { recursive: true });
        }

        const uploadedPhotos = [];
        const errors = [];

        for (const file of files) {
            if (file.size > 0 && file.name !== 'undefined') {
                try {
                    const buffer = Buffer.from(await file.arrayBuffer());
                    // Create unique filename
                    const ext = path.extname(file.name);
                    const filename = `moto_${motoId}_${Date.now()}_${Math.random().toString(36).substring(7)}${ext}`;

                    await writeFile(path.join(uploadDir, filename), buffer);

                    // Path relative to web root matching PHP logic: ./upload/moto/filename
                    const dbPath = './upload/moto/' + filename;

                    const sql = `INSERT INTO moto_fotos (motoId, caminho_foto, data_upload) VALUES (?, ?, NOW())`;
                    const result = await query(sql, [motoId, dbPath]);

                    uploadedPhotos.push({
                        id: result.insertId,
                        caminho_foto: dbPath,
                        motoId: motoId
                    });
                } catch (err) {
                    console.error('Error saving file:', file.name, err);
                    errors.push(`Failed to save ${file.name}`);
                }
            }
        }

        return NextResponse.json({ success: true, uploaded: uploadedPhotos, errors });

    } catch (error) {
        console.error('POST Error:', error);
        return NextResponse.json({ error: 'Server error: ' + error.message }, { status: 500 });
    }
}

export async function DELETE(request) {
    const { searchParams } = new URL(request.url);
    const id = searchParams.get('id');

    if (!id) {
        return NextResponse.json({ error: 'Photo ID is required' }, { status: 400 });
    }

    try {
        // Get photo path first
        const getSql = `SELECT caminho_foto FROM moto_fotos WHERE id = ?`;
        const rows = await query(getSql, [id]);

        if (rows.length === 0) {
            return NextResponse.json({ error: 'Photo not found' }, { status: 404 });
        }

        const photoPath = rows[0].caminho_foto;

        // Delete from DB
        const deleteSql = `DELETE FROM moto_fotos WHERE id = ?`;
        await query(deleteSql, [id]);

        // Delete file
        let fsPath = photoPath;
        if (fsPath.startsWith('./')) {
            fsPath = fsPath.substring(2); // remove ./
        }
        // fsPath is now 'upload/moto/filename'
        // Join with ../
        const fullPath = path.join(process.cwd(), 'public', fsPath);

        try {
            if (fs.existsSync(fullPath)) {
                await unlink(fullPath);
            }
        } catch (filesErr) {
            console.error('Error deleting file:', fullPath, filesErr);
        }

        return NextResponse.json({ success: true });
    } catch (error) {
        console.error('DELETE Error:', error);
        return NextResponse.json({ error: 'Database error' }, { status: 500 });
    }
}

export async function PUT(request) {
    try {
        const body = await request.json();
        const { id, description } = body;

        if (!id) {
            return NextResponse.json({ error: 'Photo ID is required' }, { status: 400 });
        }

        const sql = `UPDATE moto_fotos SET descricao = ? WHERE id = ?`;
        await query(sql, [description, id]);

        return NextResponse.json({ success: true });
    } catch (error) {
        console.error('PUT Error:', error);
        return NextResponse.json({ error: 'Database error' }, { status: 500 });
    }
}
