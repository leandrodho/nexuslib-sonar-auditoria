<?php

namespace App\Models;

class LibroAlpha
{
	private string $titulo;
	private string $autor;
	private string $portadaUrl;
	private string $enlaceAlpha;

	public function __construct(string $titulo, string $autor, string $portadaUrl, string $enlaceAlpha)
	{
		$this->titulo = $titulo;
		$this->autor = $autor;
		$this->portadaUrl = $portadaUrl;
		$this->enlaceAlpha = $enlaceAlpha;
	}

	public function getTitulo(): string
	{
		return $this->titulo;
	}

	public function getAutor(): string
	{
		return $this->autor;
	}

	public function getPortadaUrl(): string
	{
		return $this->portadaUrl;
	}

	public function getEnlaceAlpha(): string
	{
		return $this->enlaceAlpha;
	}
}
