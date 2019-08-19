# Mini News Crawler

This a mini program to crawl news data from an online newspaper.

## Introduction

### Installation
- PHP >= 7.0

### Getting Started
Run command in terminal:

```
php run.php
```

CSV file will be exported in following path:

```
public/export/csv/
```

### Version
- `0.3` Crawl news from thesaigontimes.vn, vnexpress.net, tuoitre.vn

### Roadmap
- `0.1` Crawl news from thesaigontimes.vn (15/08/2019)
- `0.2` Crawl news from vnexpress.net (19/08/2019)
- `0.3` Crawl news from tuoitre.vn (19/08/2019)

## Development

### Directory Structure

```
mini-news-crawler
│
├───`app`
│   │
│   └───`Common`
│   │   │
│   │   └───`Helpers`
│   │
│   ├───`Libraries`
│   │   │
│   │   └───`simplehtmldom_1_9`
│   │
│   └───`Services`
│
├───`config`
│
├───`public`
│   │
│   └───`export`
│       │
│       └───`csv`
│
```


### Library

**Simple HTML DOM**

- A HTML DOM parser written in PHP5+ let you manipulate HTML in a very easy way!
- Documentation: [https://simplehtmldom.sourceforge.io](https://simplehtmldom.sourceforge.io)


## More Information

### Sample data of CSV

|URL|Title|Author|Date|
|---|---|---|---|
|https://www.thesaigontimes.vn/121624/Cuoc-cach-mang-dau-khi-da-phien.html|Cuộc cách mạng dầu khí đá phiến|Trần Hữu Hiếu|2014-10-24 19:25:00|
|https://www.thesaigontimes.vn/274113/bao-giay-van-thu-vi.html|Báo giấy vẫn thú vị!|Lê Ngọc Quỳnh|2018-06-21 10:20:00|
|https://www.thesaigontimes.vn/274112/tiep-tuc-da-cat-giam-thu-tuc-hanh-chinh.html|Tiếp tục đà cắt giảm thủ tục hành chính|Nguyễn Đức Nguyên|2018-06-25 11:21:00|
|https://www.thesaigontimes.vn/274111/con-bao-nhieu-nhom-loi-ich.htmpdfl|Còn bao nhiêu nhóm lợi ích?|Ngọc Bình|2018-06-25 11:21:00|
|https://www.thesaigontimes.vn/274105/phe-lieu-va-logistics.html|Phế liệu và logistics|Đặng Dương|2018-06-24 9:33:00|
|https://www.thesaigontimes.vn/274104/tim-cach-chan-hang-rac-cong-nghiep-do-ve-viet-nam.html|Tìm cách chặn hàng rác công nghiệp đổ về Việt Nam|Minh Tâm|2018-06-24 9:33:00
|https://www.thesaigontimes.vn/274103/yeu-diem-boc-lo-trong-thu-thach.html|Yếu điểm bộc lộ trong thử thách|Thành Nam|2018-06-22 15:36|

