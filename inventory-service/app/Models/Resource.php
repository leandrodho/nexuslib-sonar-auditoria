<?php

class Resource
{
	private ?int $registro = null;
	private ?string $codigo = null;
	private string $titulo = '';
	private ?string $autor = null;
	private ?string $biblioteca = null;
	private ?string $tipo = null;
	private ?string $procedencia = null;
	private ?string $fecha = null;
	private string $estado = 'Disponible';

	public function __construct(array $data = [])
	{
		if (isset($data['registro'])) {
			$this->registro = (int) $data['registro'];
		}

		$this->codigo = $data['codigo'] ?? null;
		$this->titulo = $data['titulo'] ?? '';
		$this->autor = $data['autor'] ?? null;
		$this->biblioteca = $data['biblioteca'] ?? null;
		$this->tipo = $data['tipo'] ?? null;
		$this->procedencia = $data['procedencia'] ?? null;
		$this->fecha = $data['fecha'] ?? null;
		$this->estado = $data['estado'] ?? 'Disponible';
	}

	public function getRegistro(): ?int
	{
		return $this->registro;
	}

	public function setRegistro(?int $registro): void
	{
		$this->registro = $registro;
	}

	public function getCodigo(): ?string
	{
		return $this->codigo;
	}

	public function setCodigo(?string $codigo): void
	{
		$this->codigo = $codigo;
	}

	public function getTitulo(): string
	{
		return $this->titulo;
	}

	public function setTitulo(string $titulo): void
	{
		$this->titulo = $titulo;
	}

	public function getAutor(): ?string
	{
		return $this->autor;
	}

	public function setAutor(?string $autor): void
	{
		$this->autor = $autor;
	}

	public function getBiblioteca(): ?string
	{
		return $this->biblioteca;
	}

	public function setBiblioteca(?string $biblioteca): void
	{
		$this->biblioteca = $biblioteca;
	}

	public function getTipo(): ?string
	{
		return $this->tipo;
	}

	public function setTipo(?string $tipo): void
	{
		$this->tipo = $tipo;
	}

	public function getProcedencia(): ?string
	{
		return $this->procedencia;
	}

	public function setProcedencia(?string $procedencia): void
	{
		$this->procedencia = $procedencia;
	}

	public function getFecha(): ?string
	{
		return $this->fecha;
	}

	public function setFecha(?string $fecha): void
	{
		$this->fecha = $fecha;
	}

	public function getEstado(): string
	{
		return $this->estado;
	}

	public function setEstado(string $estado): void
	{
		$this->estado = $estado;
	}
}
