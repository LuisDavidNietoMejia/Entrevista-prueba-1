<?php namespace App\Clases;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Validator;
use Input;
use Redirect;
use Carbon\Carbon;
use DateTime;
use \App\Publication;
use Illuminate\Database\Eloquent\Collection as CollecionesEloquent;

class MyPublications
{
    protected static $MypublicationId;
    protected static $DateNow;

    public function __construct($MypublicationId = "")
    {
        self::$MypublicationId = publication::find($MypublicationId);
        $date = Carbon::now();
        self::$DateNow = $date->toDateString();
        // self::$Persons = null;
    }

    public static function getAllPublications() : CollecionesEloquent
    {
      // obtener todos los datos de las publicaciones:
      $publicaciones = null;

      $publicaciones = Publication::all();

      return $publicaciones;
    }

    public static function getAllLibroDiarioDatatable($field, $order, $count, $search)
    {

    // obtener todos los datos de los libros diarios:

        $librodiario = null;

        if ($search == "") {
            $librodiario = DB::table('librodiarios as a1')
                         ->join('cuentas as b2', 'a1.cuenta_id', '=', 'b2.id')
                         ->join('users as c3', 'a1.entry_by', '=', 'c3.id')
                         ->join('users as d4', 'a1.updated_by', '=', 'd4.id')
                         ->select(
                         'a1.id',
                         'a1.fecha',
                         'a1.debe',
                         'a1.haber',
                         'a1.referencia',
                         'a1.detalle',
                         'b2.nombre',
                            DB::raw('concat(c3.nombre," ",c3.apellido) as registrado_por'),
                         'a1.created_at',
                         'a1.updated_at'
                         )
                         ->orderBy($field, $order)
                         ->paginate($count);
        } else {
            $librodiario = DB::table('librodiarios as a1')
                         ->join('cuentas as b2', 'a1.cuenta_id', '=', 'b2.id')
                         ->join('users as c3', 'a1.entry_by', '=', 'c3.id')
                         ->join('users as d4', 'a1.updated_by', '=', 'd4.id')
                         ->select(
                         'a1.id',
                         'a1.fecha',
                         'a1.debe',
                         'a1.haber',
                         'a1.referencia',
                         'a1.detalle',
                         'b2.nombre',
                         DB::raw('concat(c3.nombre, " ",c3.apellido) as registrado_por'),
                         DB::raw('concat(d4.nombre, " ",d4.apellido) as registrado_por'),
                         'a1.created_at',
                         'a1.updated_at'
                         )
                         ->orWhere('a1.id', 'like', '%'.$search.'%')
                         ->orWhere('a1.debe', 'like', '%'.$search.'%')
                         ->orWhere('a1.haber', 'like', '%'.$search.'%')
                         ->orWhere('a1.referencia', 'like', '%'.$search.'%')
                         ->orWhere('a1.detalle', 'like', '%'.$search.'%')
                         ->orWhere('b2.nombre', 'like', '%'.$search.'%')
                         ->orWhere('c3.nombre AS registrado_por', 'like', '%'.$search.'%')
                         ->orWhere('d4.nombre AS actualizado_por', 'like', '%'.$search.'%')
                         ->orWhere('a1.created_at', 'like', '%'.$search.'%')
                         ->orWhere('a1.updated_at', 'like', '%'.$search.'%')
                         ->orderBy($field, $order)
                         ->paginate($count);
        }
        return $librodiario;
    }

    public static function getDataCuentas()
    {
        $cuentas = DB::table('cuentas')
                        ->select(
                        'id',
                        'nombre'
                        )
                        ->get();
        //   dd($cuentas);
        return $cuentas;
    }

    public static function validateMajorAccountDate($request)
    {
        $fecha = DB::table('librodiarios')
                             ->where('cuenta_id', $request->get('id'))
                             ->where('referencia', 'NOT LIKE', '%Saldo inicial%')
                             ->where('created_at', '<', $request->get('fecha'))
                             ->select('created_at')
                             ->get();

        return $fecha;
    }
    public static function getInitialAccount($id)
    {
        $idAccountingSeatInitial = DB::table('librodiarios')
                         ->where('cuenta_id', $id)
                         ->where('referencia', 'LIKE', '%Saldo inicial%')
                         ->select('id')
                         ->get();

        return $idAccountingSeatInitial;
    }

    public static function determineShouldHave($tipo, $saldo)
    {
        if ($tipo == 'Activo') {
            $debe = $saldo;
            $haber = "0";
        }
        if ($tipo == 'Pasivo') {
            $haber = $saldo;
            $debe = "0";
        }
        if ($tipo == 'Capital') {
            $haber = $saldo;
            $debe = "0";
        }
        if ($tipo == 'Ingresos') {
            $haber = $saldo;
            $debe = "0";
        }
        if ($tipo == 'Costos') {
            $debe = $saldo;
            $haber = "0";
        }
        if ($tipo == 'Gastos') {
            $debe = $saldo;
            $haber = "0";
        }

        //determinamos el array
        $array = array(
                "debe" => $debe,
                "haber" => $haber,
            );

        return $array;
    }

    public static function getDatatable($model, array $fields)
    {
        extract(request()->only(['query', 'limit', 'page', 'orderBy', 'ascending', 'byColumn']));

        $data = $model->select($fields);

        if (isset($query) && $query) {
            $data = $byColumn == 1 ?
                $this->filterByColumn($data, $query) :
                $this->filter($data, $query, $fields);
        }

        $count = $data->count();

        $data->limit($limit)
            ->skip($limit * ($page - 1));

        if (isset($orderBy)) {
            $direction = $ascending == 1 ? 'ASC' : 'DESC';
            $data->orderBy($orderBy, $direction);
        }

        $results = $data->get()->toArray();

        return [
            'data' => $results,
            'count' => $count,
        ];
    }

    public static function filterByColumnDatatable($data, $queries)
    {
        return $data->where(function ($q) use ($queries) {
            foreach ($queries as $field => $query) {
                if (is_string($query)) {
                    $q->where($field, 'LIKE', "%{$query}%");
                } else {
                    $start = Carbon::createFromFormat('Y-m-d', $query['start'])->startOfDay();
                    $end = Carbon::createFromFormat('Y-m-d', $query['end'])->endOfDay();

                    $q->whereBetween($field, [$start, $end]);
                }
            }
        });
    }

    public static function filterDatatable($data, $query, $fields)
    {
        return $data->where(function ($q) use ($query, $fields) {
            foreach ($fields as $index => $field) {
                $method = $index ? 'orWhere' : 'where';
                $q->{$method}($field, 'LIKE', "%{$query}%");
            }
        });
    }

    // public static function getCupos($charlaFechaId){
    //
    // 	$datenow = self::$DateNow;
    // 	// obtener datos de las charlas condicionalmente segun status,fecha,cupos:
    // 	$cupos = Charlafechas::select('cupos_disponibles')
    // 					 ->where('id', $charlaFechaId)
    // 					 ->where('fecha', '>=', $datenow)
    // 					 ->where('estatus_id', '=', 'pendiente')
    // 					 ->first();
    //
    // 	 return $cupos;
    // }
    //
    // public static function getValidatePersons($request){
    //
    // 	$person = self::$Persons;
  //   $charlaid = self::$MyCharla;
    //
    // 	foreach ($request->persona_id as $key => $value) {
    //
    // 		 $count = Charlapersonas::join('tbl_charlas', 'tbl_charla_personas.charla_id', '=', 'tbl_charlas.id')
    // 			  ->where('tbl_charla_personas.persona_id', '=', $request->persona_id[$key])
    // 				->where('tbl_charlas.id', '=', $charlaid->id )
    // 				->WhereIn('tbl_charla_personas.estatus_persona', ['inasistente'])
    // 				->count();
    //
    // 		if ($count != 0) {
    // 		 	  $person = $request->persona_id[$key];
    // 			  return $person;
    // 		 	}
    // 	 }
    // 	 return $person;
    // }
    //
    // public static function getAvailableDates(){
    //
    // 	$datenow = self::$DateNow;
    // 	$charlaid = self::$MyCharla;
    // 	// // obtener datos de las charlas condicionalmente segun status,fecha,cupos:
    // 	$dateCharlas = DB::table('tbl_charlas')
    // 											->join('tbl_charla_fechas', 'tbl_charlas.id', '=', 'tbl_charla_fechas.charla_id')
    // 											->where('tbl_charla_fechas.estatus_id', '=', 'pendiente')
    // 											->where('tbl_charla_fechas.cupos_disponibles', '>', '0')
    // 											->where('tbl_charla_fechas.fecha', '>=', $datenow)
    // 											->where('tbl_charlas.id', '=', $charlaid->id)
    // 											->orderBy('tbl_charla_fechas.fecha', 'ASC')
    // 											->select(
    // 													'tbl_charla_fechas.id',
    // 													'tbl_charla_fechas.fecha',
    // 													'tbl_charla_fechas.cupos_disponibles',
    // 													'tbl_charlas.description'
    // 										 )->get();
    //
    // 		return $dateCharlas;
    // }
    //
    // public static function getAvailablePersons(){
    //
    // 	$datenow = self::$DateNow;
    // 	$charlaid = self::$MyCharla;
    // 	$lobjFiltro = \MySourcing::getFiltroUsuario(3,1);
    //
    // 	//DB::enableQueryLog();
    //
    // 	// // obtener datos de las charlas condicionalmente segun status,fecha,cupos:
    // 	$personCharlas = DB::table('tbl_personas')
    // 					->whereNotExists(function ($query) use ($charlaid) {
    // 							$query->select(DB::raw(1))
    // 									->from('tbl_charla_personas')
    // 									->whereRaw('tbl_charla_personas.persona_id = tbl_personas.IdPersona')
    // 									->Join('tbl_charlas', 'tbl_charla_personas.charla_id', '=', 'tbl_charlas.id')
    // 									->Join('tbl_charla_fechas', 'tbl_charla_fechas.charla_id', '=', 'tbl_charlas.id')
    // 									->where('tbl_charlas.id', '=', $charlaid->id)
    // 									->Where('tbl_charla_fechas.estatus_id','pendiente');
    // 					})
    // 					->LeftJoin('tbl_contratistas', 'tbl_personas.entry_by_access', '=', 'tbl_contratistas.entry_by_access')
  //    					->LeftJoin('tbl_contratos_personas', 'tbl_personas.IdPersona', '=', 'tbl_contratos_personas.IdPersona')
  //    					->LeftJoin('tbl_contrato', 'tbl_contrato.contrato_id', '=', 'tbl_contratos_personas.contrato_id')
  //    					->LeftJoin('tbl_roles', 'tbl_roles.IdRol', '=', 'tbl_contratos_personas.IdRol')
  //    					->LeftJoin('tbl_nacionalidad', 'tbl_nacionalidad.id_Nac', '=', 'tbl_personas.id_Nac')
  //    					->whereNotExists(function ($query) {
  //    							$query->select(DB::raw(1))
    // 									->from('tbl_documentos')
    // 									->join('tbl_tipos_documentos', 'tbl_documentos.IdTipoDocumento', '=', 'tbl_tipos_documentos.IdTipoDocumento')
    // 									->whereraw('tbl_documentos.Entidad = 3') //documento de personas
    // 									->whereraw('tbl_documentos.IdEntidad = tbl_personas.IdPersona')
    // 									->whereraw('tbl_documentos.IdEstatus = 5')
    // 									->whereraw('ifnull(tbl_documentos.IdEstatusDocumento,1) = 1')
    // 									->whereraw('tbl_tipos_documentos.IdProceso = 6'); //asistencia
  //    					})
  //    					->whereIn('tbl_contratistas.IdContratista',$lobjFiltro['contratistas'])
    // 					->select(
    // 							'tbl_personas.IdPersona',
    // 							'tbl_personas.RUT',
    // 							'tbl_personas.Nombres',
    // 							'tbl_personas.Apellidos',
    // 							'tbl_nacionalidad.nacionalidad as Nacionalidad',
    // 							DB::raw('DATE_FORMAT(tbl_personas.FechaNacimiento, "%d/%m/%Y") as FechaNacimiento'),
    // 							'tbl_contratistas.RazonSocial',
    // 							'tbl_contrato.cont_numero',
    // 							'tbl_roles.Descripción as Rol'
    // 					)->get();
    //
    // 		  return $personCharlas;
    // }
    //
    // public static function getPerson($charlapersona){
    //
    // 	$datenow = self::$DateNow;
    // 	$charlaid = self::$MyCharla;
    //
    // 	$lobjFiltro = \MySourcing::getFiltroUsuario(3,1);
    //
    // 	$person = DB::table('tbl_personas')
    // 			->Join('tbl_charla_personas', 'tbl_personas.IdPersona', '=', 'tbl_charla_personas.persona_id')
    // 			->Join('tbl_charlas', 'tbl_charla_personas.charla_id', '=', 'tbl_charlas.id')
    // 			->where('tbl_charlas.id', '=', $charlaid->id)
    // 			->where('tbl_charla_personas.id', '=', $charlapersona)
    // 			->select(
    // 				'tbl_personas.IdPersona',
    // 				'tbl_personas.RUT',
    // 				'tbl_personas.Nombres',
    // 				'tbl_personas.Apellidos',
    // 				'tbl_personas.Direccion',
    // 				'tbl_personas.FechaNacimiento'
    // 		    )
    // 			->LeftJoin('tbl_contratistas', 'tbl_personas.entry_by_access', '=', 'tbl_contratistas.entry_by_access')
    // 			->whereIn('tbl_contratistas.IdContratista',$lobjFiltro['contratistas'])
    // 			->get();
    //
    // 		  return $person;
    // }
    //
    // public static function getPersonsCharla($charlafecha){
    //
    // 	$personCharlas = DB::table('tbl_personas')
    // 									->Join('tbl_charla_personas', 'tbl_personas.IdPersona', '=', 'tbl_charla_personas.persona_id')
    // 									->Join('tbl_charlas', 'tbl_charla_personas.charla_id', '=', 'tbl_charlas.id')
    // 									->Join('tbl_charla_fechas', 'tbl_charla_personas.charlafecha_id', '=', 'tbl_charla_fechas.id')
    // 									->LeftJoin('tbl_contratistas', 'tbl_personas.entry_by_access', '=', 'tbl_contratistas.entry_by_access')
    // 									->where('tbl_charla_fechas.id', '=', $charlafecha)
    // 									->select(
    // 										  'tbl_personas.IdPersona',
    // 										  'tbl_contratistas.RazonSocial',
    // 											'tbl_contratistas.Email',
    // 											'tbl_personas.RUT',
    // 											 DB::raw('concat(tbl_personas.Nombres, " ", tbl_personas.Apellidos) as Nombres'),
    // 											 'tbl_charla_personas.estatus_persona'
    // 									 )->get();
    //
    // 			return $personCharlas;
    // }
    //
    // public static function getPersonsCharlaEmail($charlafecha){
    //
    // 	$personCharlas = DB::table('tbl_personas')
    // 									->Join('tbl_charla_personas', 'tbl_personas.IdPersona', '=', 'tbl_charla_personas.persona_id')
    // 									->Join('tbl_charlas', 'tbl_charla_personas.charla_id', '=', 'tbl_charlas.id')
    // 									->Join('tbl_charla_fechas', 'tbl_charla_personas.charlafecha_id', '=', 'tbl_charla_fechas.id')
    // 									->LeftJoin('tbl_contratistas', 'tbl_personas.entry_by_access', '=', 'tbl_contratistas.entry_by_access')
    // 									->where('tbl_charla_fechas.id', '=', $charlafecha)
    // 									// ->groupBy('tbl_contratistas.Email')
    // 									->select(
    // 										'tbl_personas.IdPersona',
    // 										'tbl_personas.RUT',
    // 										 DB::raw('concat(tbl_personas.Nombres, " ", tbl_personas.Apellidos) as nombre'),
    // 										'tbl_charla_fechas.fecha',
    // 										'tbl_contratistas.IdContratista',
    // 										'tbl_contratistas.RazonSocial',
    // 										'tbl_contratistas.Email',
    // 										'tbl_charlas.name',
    // 										'tbl_charlas.description'
  //           				)->get();
    //
    // 			return $personCharlas;
    // }
    //
    // public static function getPersonsCharlaCount($charlafecha){
    //
    // 	$personCharlas = DB::table('tbl_personas')
    // 									->Join('tbl_charla_personas', 'tbl_personas.IdPersona', '=', 'tbl_charla_personas.persona_id')
    // 									->Join('tbl_charlas', 'tbl_charla_personas.charla_id', '=', 'tbl_charlas.id')
    // 									->Join('tbl_charla_fechas', 'tbl_charla_personas.charlafecha_id', '=', 'tbl_charla_fechas.id')
    // 									// ->LeftJoin('tbl_contratistas', 'tbl_personas.entry_by_access', '=', 'tbl_contratistas.entry_by_access')
    // 									->where('tbl_charla_fechas.id', '=', $charlafecha)
    // 								  ->count();
    //
    // 			return $personCharlas;
    // }
    //
    //
    // public static function getDetailCharlaFecha($charlafecha){
    //
    // 	$charlaid = self::$MyCharla;
    //
    // 	$detailCharlaFecha = Charlafechas::where('charla_id', '=', $charlaid->id)
    // 									->where('tbl_charla_fechas.id', '=', $charlafecha)
    // 									->Join('tbl_charlas', 'tbl_charla_fechas.charla_id', '=', 'tbl_charlas.id')
    // 									->first();
    //
    // 	return $detailCharlaFecha;
    //
    // }
    //
    // public static function getDetailCharla(){
    //
    // 	$charlaid = self::$MyCharla;
    // 	$detailCharla = Charlas::where('id', '=', $charlaid->id)
    // 											 ->first();
    // 	return $detailCharla;
    //
    // }
    //
    // public static function getValidatePersonsBd($persons,$idRequest){
    //
    // 	$person = self::$Persons;
    //
    // 	foreach ($persons as $key => $value) {
    //
    // 		 $count = Charlapersonas::join('tbl_charlas', 'tbl_charla_personas.charla_id', '=', 'tbl_charlas.id')
    // 			  ->where('tbl_charla_personas.persona_id', '=', $persons[$key]->IdPersona)
    // 				->where('tbl_charlas.id', '=', $idRequest)
    // 				->WhereIn('tbl_charla_personas.estatus_persona', ['inasistente'])
    // 				->count();
    //
    // 		if ($count != 0) {
    // 		 	  $person = $persons[$key]->IdPersona;
    // 			  return $person;
    // 		 	}
    // 	 }
    // 	 return $person;
    // }
    //
    // public static function FormatDate($fecha){
    //
    //   $date = Carbon::createFromFormat('d/m/Y', $fecha)->format('Y-m-d');
    // 	return $date;
    //
    // }

    // static public function getRequirements($id){
    //
    // 	// $lobjRequisitos = Charlas::where('id',$id)->get();
    // 	// if ($lobjRequisitos){
    // 	// return $lobjRequisitos;
    // 	// }
    //
    // }
}
