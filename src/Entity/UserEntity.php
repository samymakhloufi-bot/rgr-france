<?php 

class User{
    private int $id_user;
    private string $nom;
    private string $prenom;
    private string $email;
    private string $password;
    private string $role;
    private ?string $tel;
    private int $actif;

    public function __construct(array $data) {
        $this -> id_user = (int)$data['id_user'];
        $this ->nom          = $data['nom'];
        $this ->prenom       = $data['prenom'];
        $this ->email        = $data['email'];
        $this ->password     = $data['password'];
        $this ->role         = $data['role'];
        $this ->tel          = $data['tel'];
        $this ->actif        = (int)($data['actif'] ?? 1);
        
    }
    
    public function getId(): int                {return $this->id_user;}
    public function getNom(): string            {return $this->nom;}
    public function getPrenom():string          {return $this->prenom;}
    public function getEmail():string           {return $this->email;}
    public function getPassword():string        {return $this->password;}
    public function getRole():string            {return $this->role;}
    public function getTel(): ?string           {return $this->tel;}
    public function getActif(): bool            {return $this->actif;}
    
    
    public function getFullName(): string{
        return $this->prenom . ' ' . $this->nom;
    }
}