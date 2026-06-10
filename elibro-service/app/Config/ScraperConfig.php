<?php

namespace App\Config;

class ScraperConfig
{
	public const BASE_URL = 'https://elibro.ejemplo.com';
	public const BOOK_CONTAINER_SELECTOR = '.book-item';
	public const TITLE_SELECTOR = '.book-title';
	public const AUTHOR_SELECTOR = '.book-author';
	public const COVER_SELECTOR = '.book-cover img';
	public const LINK_SELECTOR = '.book-link';
}
