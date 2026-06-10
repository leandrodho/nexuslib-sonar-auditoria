<?php

class ReservedBook
{
	private ?int $id = null;
	private string $user_uuid = '';
	private string $codigo = '';
	private ?int $registro = null;
	private string $estado = 'Pendiente';
	private ?string $fecha_reserva = null;

	public function __construct(array $data = [])
	{
		if (isset($data['id'])) {
			$this->id = (int) $data['id'];
		}

		$this->user_uuid = $data['user_uuid'] ?? '';
		$this->codigo = (string) ($data['codigo'] ?? '');
		$this->registro = isset($data['registro']) ? (int) $data['registro'] : null;
		$this->estado = $data['estado'] ?? 'Pendiente';
		$this->fecha_reserva = $data['fecha_reserva'] ?? null;
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

	public function getRegistro(): ?int
	{
		return $this->registro;
	}

	public function setRegistro(?int $registro): void
	{
		$this->registro = $registro;
	}

	public function getEstado(): string
	{
		return $this->estado;
	}

	public function setEstado(string $estado): void
	{
		$this->estado = $estado;
	}

	public function getFechaReserva(): ?string
	{
		return $this->fecha_reserva;
	}

	public function setFechaReserva(?string $fecha_reserva): void
	{
		$this->fecha_reserva = $fecha_reserva;
	}
}
