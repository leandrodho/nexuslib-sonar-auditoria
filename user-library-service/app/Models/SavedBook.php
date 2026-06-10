<?php

class SavedBook
{
	private ?int $id = null;
	private string $user_uuid = '';
	private string $codigo = '';
	private string $origen = '';
	private string $titulo = '';
	private ?string $portada_url = null;
	private ?string $fecha_guardado = null;

	public function __construct(array $data = [])
	{
		if (isset($data['id'])) {
			$this->id = (int) $data['id'];
		}

		$this->user_uuid = $data['user_uuid'] ?? '';
		$this->codigo = $data['codigo'] ?? '';
		$this->origen = $data['origen'] ?? '';
		$this->titulo = $data['titulo'] ?? '';
		$this->portada_url = $data['portada_url'] ?? null;
		$this->fecha_guardado = $data['fecha_guardado'] ?? null;
	}

	public function getId(): ?int
	{
		return $this->id;
	}

	public function setId(?int $id): void
	{
		$this->id = $id;
	}

	public function getUserUuid(): string
	{
		return $this->user_uuid;
	}

	public function setUserUuid(string $user_uuid): void
	{
		$this->user_uuid = $user_uuid;
	}

	public function getCodigo(): string
	{
		return $this->codigo;
	}

	public function setCodigo(string $codigo): void
	{
		$this->codigo = $codigo;
	}

	public function getOrigen(): string
	{
		return $this->origen;
	}

	public function setOrigen(string $origen): void
	{
		$this->origen = $origen;
	}

	public function getTitulo(): string
	{
		return $this->titulo;
	}

	public function setTitulo(string $titulo): void
	{
		$this->titulo = $titulo;
	}

	public function getPortadaUrl(): ?string
	{
		return $this->portada_url;
	}

	public function setPortadaUrl(?string $portada_url): void
	{
		$this->portada_url = $portada_url;
	}

	public function getFechaGuardado(): ?string
	{
		return $this->fecha_guardado;
	}

	public function setFechaGuardado(?string $fecha_guardado): void
	{
		$this->fecha_guardado = $fecha_guardado;
	}
}
