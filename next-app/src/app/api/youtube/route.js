import { NextResponse } from 'next/server';
import fs from 'fs';
import path from 'path';

export async function GET() {
    try {
        // Path to the cache file (root of the project)
        const cachePath = path.join(process.cwd(), '..', 'youtube_videos_cache.json');

        if (!fs.existsSync(cachePath)) {
            return NextResponse.json([]);
        }

        const data = fs.readFileSync(cachePath, 'utf8');
        const videos = JSON.parse(data);

        // Shuffle and pick 9 videos as done in PHP
        const shuffled = videos.sort(() => 0.5 - Math.random());
        const selected = shuffled.slice(0, 9);

        return NextResponse.json(selected);
    } catch (err) {
        console.error('Error reading youtube cache:', err);
        return NextResponse.json([]);
    }
}
