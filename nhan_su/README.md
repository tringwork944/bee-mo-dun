# Mo dun Nhan su

## 1. Tong quan
Mo dun `nhan_su` tuong thich voi logic module moi cua bee-framework.
Module nay khai bao metadata, lifecycle, menu, permission, route, migration, seed va asset qua `cau_hinh.php` va thu muc `co_so_du_lieu/`.

Tinh nang chinh:
- Tong quan nhan su
- Quan ly nhan vien
- Quan ly phong ban
- Quan ly chuc vu
- Cham cong
- Ma tran cham cong theo nhan vien / thang
- Dashboard ApexCharts local

## 2. Dependency
- `xac_thuc`
- `tai_khoan`

Khi kich hoat, module se kiem tra ca hai dependency dang o trang thai `dang_bat`.

## 3. Metadata va registry
Framework hien tai doc metadata tu `cau_hinh.php` voi cac key:
- `ma`
- `ten`
- `mo_ta`
- `phu_thuoc`
- `menu`
- `route`
- `quyen`
- `tai_nguyen`

`nhan_su` dang ky:
- menu sidebar qua CSDL
- route module qua registry loader
- permission module qua manifest
- asset local qua `tai_nguyen`

## 4. Lifecycle
### Cai dat
- Chay `co_so_du_lieu/migration.sql`
- Chay `co_so_du_lieu/seed.sql`
- Dong bo menu va permission tu `cau_hinh.php`
- Seed demo hien tai gom `phong_ban`, `chuc_vu` va mot ban ghi cham cong mau neu co du lieu nhan vien

### Kich hoat
- Kiem tra `xac_thuc` va `tai_khoan` dang hoat dong
- Bat menu module trong CSDL
- Khong seed lai du lieu gay duplicate

### Tat
- An menu module trong CSDL
- Khong xoa du lieu nghiep vu `nhan_vien`, `phong_ban`, `chuc_vu`, `cham_cong`

### Go cai dat
- Framework chay `co_so_du_lieu/uninstall.sql`
- Framework cleanup menu / permission / registry theo logic uninstall moi
- `uninstall.sql` cua `nhan_su` se drop sach bang nghiep vu cua module
- Khong xoa thu muc code module

## 5. Menu
Menu duoc seed tu metadata va luu trong `menu_he_thong`, khong hardcode sidebar:
- Nhan su
- Tong quan
- Nhan vien
- Phong ban
- Chuc vu
- Cham cong

Permission menu:
- Tong quan: `nhan_su.dashboard`
- Nhan vien: `nhan_vien.xem`
- Phong ban: `phong_ban.xem`
- Chuc vu: `chuc_vu.xem`
- Cham cong: `cham_cong.xem`

## 6. Permission
- `nhan_su.dashboard`
- `nhan_su.quan_ly`
- `nhan_vien.xem`
- `nhan_vien.them`
- `nhan_vien.sua`
- `nhan_vien.xoa`
- `phong_ban.xem`
- `phong_ban.them`
- `phong_ban.sua`
- `phong_ban.xoa`
- `chuc_vu.xem`
- `chuc_vu.them`
- `chuc_vu.sua`
- `chuc_vu.xoa`
- `cham_cong.xem`
- `cham_cong.them`
- `cham_cong.sua`
- `cham_cong.xoa`

## 7. Route chinh
Dashboard:
- `GET /hr/dashboard`
- `GET /nhan-su/tong-quan`
- `GET /nhan-su/api/thong-ke`

Nhan vien:
- `GET /nhan-su/nhan-vien`
- `GET /nhan-su/nhan-vien/them`
- `POST /nhan-su/nhan-vien/luu`
- `GET /nhan-su/nhan-vien/{id}`
- `GET /nhan-su/nhan-vien/sua/{id}`
- `POST /nhan-su/nhan-vien/cap-nhat/{id}`
- `POST /nhan-su/nhan-vien/tao-tai-khoan/{id}`
- `POST /nhan-su/nhan-vien/xoa/{id}`

Phong ban:
- `GET /nhan-su/phong-ban`
- `GET /nhan-su/phong-ban/them`
- `POST /nhan-su/phong-ban/luu`
- `GET /nhan-su/phong-ban/sua/{id}`
- `POST /nhan-su/phong-ban/cap-nhat/{id}`
- `POST /nhan-su/phong-ban/xoa/{id}`

Chuc vu:
- `GET /nhan-su/chuc-vu`
- `GET /nhan-su/chuc-vu/them`
- `POST /nhan-su/chuc-vu/luu`
- `GET /nhan-su/chuc-vu/sua/{id}`
- `POST /nhan-su/chuc-vu/cap-nhat/{id}`
- `POST /nhan-su/chuc-vu/xoa/{id}`

Cham cong:
- `GET /nhan-su/cham-cong`
- `GET /nhan-su/cham-cong/calendar`
- `GET /nhan-su/cham-cong/monthly-matrix`
- `GET /nhan-su/cham-cong/them`
- `POST /nhan-su/cham-cong/luu`
- `GET /nhan-su/cham-cong/{id}`
- `GET /nhan-su/cham-cong/sua/{id}`
- `POST /nhan-su/cham-cong/cap-nhat/{id}`
- `POST /nhan-su/cham-cong/xoa/{id}`

## 8. Database
Bang module:
- `phong_ban`
- `chuc_vu`
- `nhan_vien`
- `cham_cong`

Rang buoc hien tai:
- `nhan_vien.ma_nhan_vien` unique
- `nhan_vien.email` unique
- `nhan_vien.account_id` nullable unique
- `phong_ban.ma_phong_ban` unique
- `chuc_vu.ma_chuc_vu` unique
- `cham_cong (nhan_vien_id, ngay)` unique
- index `cham_cong.ngay`
- index `cham_cong.trang_thai`

Migration duoc viet theo `CREATE TABLE IF NOT EXISTS` de chay lai an toan.

## 9. UI va asset
- UI dung Tabler
- Text tieng Viet khong dau
- ApexCharts load tu asset local, khong dung CDN
- Asset dang ky trong `cau_hinh.php` qua `tai_nguyen`
- View khong nhung truc tiep CSS/JS module nua
- Cham cong giu matrix compact va highlight T7 / CN

## 10. Cach test
1. Cai dat module `nhan_su` trong Quan ly mo dun.
2. Kich hoat module `nhan_su`.
3. Kiem tra menu sidebar chi hien 1 nhom `Nhan su`.
4. Mo dashboard va trang cham cong de xac nhan asset load dung.
5. Tat module va kiem tra menu bien mat nhung bang nghiep vu van con.
6. Kich hoat lai de kiem tra menu khong duplicate.
7. Go cai dat de framework chay `uninstall.sql` va cleanup registry.
8. Cai dat lai sau uninstall de xac nhan migration / seed / menu / permission khong duplicate.
