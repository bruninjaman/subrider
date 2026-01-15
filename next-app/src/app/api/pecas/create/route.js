import { query } from '@/lib/db';
import { NextResponse } from 'next/server';
import { writeFile } from 'fs/promises';
import path from 'path';

export async function POST(request) {
    try {
        const formData = await request.formData();
        const grupo = formData.get('grupo');
        const item = formData.get('item');
        const parte = formData.get('parte');
        const file = formData.get('foto');

        let fotoPath = '';

        if (file && file.size > 0) {
            const buffer = Buffer.from(await file.arrayBuffer());
            const filename = Date.now() + "_" + file.name.replaceAll(" ", "_");

            // Define the upload directory
            // We need to save to c:\xampp\htdocs\subrider\upload\peca
            // Since we are in c:\xampp\htdocs\subrider\next-app, we go up one level.
            // Using absolute path for safety if possible, or relative.

            // Assuming the server process runs in the project root.
            const uploadDir = path.join(process.cwd(), 'public', 'upload', 'peca');

            try {
                await writeFile(path.join(uploadDir, filename), buffer);
                fotoPath = `upload/peca/${filename}`;
            } catch (err) {
                console.error("Error saving file:", err);
                // Fallback or error?
                // If the directory doesn't exist, we might fail. 
                // But the directory should exist from the PHP app.
                return NextResponse.json({ error: 'Failed to save image' }, { status: 500 });
            }
        }

        const result = await query(
            'INSERT INTO pecas (grupo, item, parte, foto) VALUES (?, ?, ?, ?)',
            [grupo, item, parte, fotoPath]
        );

        return NextResponse.json({ success: true, id: result.insertId });
    } catch (error) {
        console.error('Create Error:', error);
        return NextResponse.json({ error: 'Database error: ' + error.message }, { status: 500 });
    }
}
