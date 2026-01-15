import mysql from 'mysql2/promise';

let pool;

export async function getDb() {
    if (!pool) {
        pool = mysql.createPool({
            host: process.env.MYSQL_HOST,
            user: process.env.MYSQL_USER,
            password: process.env.MYSQL_PASSWORD,
            database: process.env.MYSQL_DATABASE,
            waitForConnections: true,
            connectionLimit: 10,
            queueLimit: 0,
        });
    }
    return pool;
}

export async function query(sql, params) {
    const db = await getDb();
    const [results] = await db.execute(sql, params);
    return results;
}
