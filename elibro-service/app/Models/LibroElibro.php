<?php

namespace App\Models;

class LibroElibro
{
	private string $titulo;
	private string $autor;
	private string $portadaUrl;
	private string $enlaceElibro;

	public function __construct(string $titulo, string $autor, string $portadaUrl, string $enlaceElibro)
	{
		$this->titulo = $titulo;
		$this->autor = $autor;
		$this->portadaUrl = $portadaUrl;
		$this->enlaceElibro = $enlaceElibro;
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

	public function getEnlaceElibro(): string
	{
		return $this->enlaceElibro;
	}
}
