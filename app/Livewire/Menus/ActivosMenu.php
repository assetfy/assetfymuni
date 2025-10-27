<?php

namespace App\Livewire\Menus;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\ActivosModel;
use App\Models\TiposModel;
use App\Helpers\IdHelper;

class ActivosMenu extends Component
{
    use WithPagination;

    public $search = '';
    public $sort = 'id_tipo';
    public $direction = 'desc';
    public $activos, $user, $cuit;

    protected $paginationTheme = 'bootstrap';
    protected $updatesQueryString = ['search'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function mount()
    {
        $this->loadData();
    }

    public function render()
    {
        $this->loadData();

        // Obtener los tipos paginados y filtrados por búsqueda
        $tipos = TiposModel::where('nombre', 'like', '%' . $this->search . '%')
            ->orderBy($this->sort, $this->direction)
            ->paginate(8);

        return view('livewire.menus.activos-menu', [
            'tipos' => $tipos,
            'activos' => $this->activos,
        ]);
    }
    
    public function loadData()
    {
        $identificadores = IdHelper::identificadorParcial();
        $this->user = $identificadores['user'];
        $this->cuit = $identificadores['cuit'];

        $query = ActivosModel::query();

        if ($this->user !== null) {
            $query->where('usuario_titular', $this->user);
        } else {
            $query->where('empresa_titular', $this->cuit);
        }

        $this->activos = $query->get();
        $this->activos = $this->activos ?: collect();
    }
}
