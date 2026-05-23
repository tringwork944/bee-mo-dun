# NextDNS Module

Mo dun tich hop NextDNS cho Bee Framework, ho tro quan ly profile, thong ke DNS, kiem tra ten mien va quan ly danh sach chan/cho phep.

## Tinh nang

- [x] Tong quan thong ke DNS
- [x] Bieu do ApexCharts cuc bo
- [x] Quan ly nhieu profile NextDNS
- [x] Quan ly bo loc
- [x] Quan ly danh sach chan
- [x] Quan ly danh sach cho phep
- [x] Kiem tra ten mien bi chan
- [x] Cache du lieu thong ke
- [x] Phan quyen theo tai khoan
- [x] Tabler UI
- [x] Responsive
- [x] Popup/modal thao tac

## Cau truc menu

```text
NextDNS
|-- Tong quan
|-- Kiem tra ten mien
|-- Quan ly profile
|-- Quan ly bo loc
|-- Danh sach chan
`-- Danh sach cho phep
```

## Phan quyen

### Tai khoan thuong

Co the duoc cap quyen:

- Xem tong quan
- Kiem tra ten mien

### Quan tri

Co the duoc cap toan bo quyen cua mo dun.

Danh sach permission:

- `xem_nextdns`
- `kiem_tra_ten_mien_nextdns`
- `quan_ly_profile_nextdns`
- `quan_ly_bo_loc_nextdns`
- `quan_ly_danh_sach_chan_nextdns`
- `quan_ly_danh_sach_cho_phep_nextdns`
- `cap_nhat_du_lieu_nextdns`

## Cai dat

1. Tai mo dun
2. Cai dat tu Quan ly mo dun
3. Them profile NextDNS
4. Dong bo du lieu

## Su dung

### Tong quan

Hien thi:

- Tong truy van
- Bi chan
- Duoc cho phep
- Mac dinh
- Xu huong truy van
- Top domain
- Bo loc chan

### Kiem tra ten mien

Cho phep:

- Chon profile
- Nhap domain
- Kiem tra trang thai
- Hien thi ket qua bang popup

### Quan ly profile

Cho phep:

- Them
- Sua
- Xoa
- Bat/Tat

### Quan ly bo loc

Cho phep:

- Xem bo loc tu cache
- Dong bo du lieu bo loc

### Danh sach chan

Cho phep:

- Them domain
- Bat/Tat
- Xoa
- Dong bo

### Danh sach cho phep

Cho phep:

- Them domain
- Bat/Tat
- Xoa
- Dong bo

## Giao dien

- Tabler UI
- Tabler Icons
- ApexCharts cuc bo
- Responsive desktop/mobile

## Luu y

- Khong tu goi NextDNS API khi mo trang neu da co cache
- Du lieu tong quan uu tien doc tu cache, chi cap nhat khi nguoi dung thao tac hoac chua co cache
- Du lieu bo loc doc tu cache, chi dong bo khi nguoi dung bam dong bo
- API key duoc ma hoa truoc khi luu
- API key khong hien thi cong khai
- API key khong luu trong HTML hoac JavaScript

## CSDL

Bang dang duoc su dung:

- `nextdns_profile`
- `nextdns_cache_thong_ke`
- `nextdns_cache_bo_loc`
- `nextdns_danh_sach_chan`
- `nextdns_danh_sach_cho_phep`

## Changelog

### v1.0.x

- Tich hop NextDNS API
- Them bieu do thong ke
- Quan ly profile
- Quan ly bo loc
- Kiem tra ten mien
- Quan ly danh sach chan/cho phep
- Cap nhat UI dong bo
- Them phan quyen
