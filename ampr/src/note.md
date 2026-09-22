1. Connection string
   Copy the connection details for your database.
   Details:
   If your database password contains special characters, percent-encode them in the connection string.
   Connection parameters
   host:db.jcjhgytlpfovswswcskx.supabase.co
   port:5432
   database:postgres
   user:postgres
   Code:
   File: Code

```
postgresql://postgres:[YOUR-PASSWORD]@db.jcjhgytlpfovswswcskx.supabase.co:5432/postgres
```

2. Install Agent Skills (optional)
   Agent Skills give AI coding tools ready-made instructions, scripts, and resources for working with Supabase more accurately and efficiently.
   Code:
   File: Code

```
npx skills add supabase/agent-skills
```

jcjhgytlpfovswswcskx
MjIGBpRqRM398uRn

[satella@iZt4n9t0wosidr1lpol71zZ migration]$ sudo docker run --rm --dns 8.8.8.8 -v $(pwd)/migration.load:/migration.load dimitri/pgloader:latest pgloader /migration.load
2026-07-27T04:46:39.025001Z LOG pgloader version "3.6.7~devel"
KABOOM!
DB-CONNECTION-ERROR: Failed to connect to pgsql at "aws-0-ap-northeast-2.pooler.supabase.com" (port 5432) as user "postgres%2Ejcjhgytlpfovswswcskx": Database error XX000: (ENOIDENTIFIER) no tenant identifier provided (external_id or sni_hostname required)
An unhandled error condition has been signalled:
Failed to connect to pgsql at "aws-0-ap-northeast-2.pooler.supabase.com" (port 5432) as user "postgres%2Ejcjhgytlpfovswswcskx": Database error XX000: (ENOIDENTIFIER) no tenant identifier provided (external_id or sni_hostname required)

What I am doing here?

Failed to connect to pgsql at "aws-0-ap-northeast-2.pooler.supabase.com" (port 5432) as user "postgres%2Ejcjhgytlpfovswswcskx": Database error XX000: (ENOIDENTIFIER) no tenant identifier provided (external_id or sni_hostname required)

[satella@iZt4n9t0wosidr1lpol71zZ migration]$ ls
migration.load
[satella@iZt4n9t0wosidr1lpol71zZ migration]$ cat migration.load
LOAD DATABASE
FROM mysql://ampr:355b1jqGKCtvwZybBxPUzXO@host.docker.internal:3306/mariadb
INTO postgres://postgres%2Ejcjhgytlpfovswswcskx:MjIGBpRqRM@aws-0-ap-northeast-2.pooler.supabase.com:5432/postgres

WITH include drop, create tables, create indexes, reset sequences, foreign keys

CAST type datetime to timestamp drop default drop not null using zero-dates-to-null,
type date drop not null drop default using zero-dates-to-null;
[satella@iZt4n9t0wosidr1lpol71zZ migration]$
