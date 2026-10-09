# Skema database

Database `shorturl` (MySQL 8.4, `utf8mb4_unicode_ci`). Semua tabel dibuat lewat migration di `database/migrations/`.

## `users`
| Kolom | Tipe | Catatan |
|---|---|---|
| id | bigint PK | |
| name, email | varchar | email unik |
| password | varchar | hash bcrypt |
| role | enum(`admin`,`member`) | default `member` |
| is_active | bool | akun nonaktif tidak bisa login dan link-nya berhenti mengalihkan |
| remember_token, email_verified_at, timestamps | | bawaan Laravel |

## `short_links`
| Kolom | Tipe | Catatan |
|---|---|---|
| id | bigint PK | |
| user_id | FK → users (cascade) | index |
| alias | varchar(64) **UNIQUE** | selalu huruf kecil |
| destination_url | text | hanya http/https |
| title | varchar null | |
| is_active | bool | |
| expires_at | timestamp null | |
| timestamps | | |

## `click_events`
| Kolom | Tipe | Catatan |
|---|---|---|
| id | bigint PK | |
| short_link_id | FK → short_links (cascade) | |
| clicked_at | timestamp | |
| referer_host | varchar(255) null | hostname saja, bukan URL lengkap |
| user_agent_family | varchar(50) null | Chrome/Firefox/Safari/Edge/Bot/Other… |
| country_code | char(2) null | disiapkan, belum diisi |

Index: `(short_link_id, clicked_at)`. **Tidak ada kolom IP.**

## `audit_logs`
| Kolom | Tipe | Catatan |
|---|---|---|
| id | bigint PK | |
| actor_user_id | FK → users (null on delete) | |
| action | varchar(64) | `auth.login`, `link.created`, `user.deactivated`, … |
| subject_type, subject_id | | mis. `ShortLink` #12 |
| metadata | json null | tanpa password/rahasia |
| created_at | timestamp | |

Index: `(actor_user_id, created_at)`, `created_at`.

Tabel bawaan Laravel lain: `sessions`, `cache`, `cache_locks`, `jobs`, `password_reset_tokens`, `migrations`.
