<?php namespace App\Http\Controllers;

use Request;
use App\CalendarCategories;
use App\CalendarCategoriesEntry;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use DB;

class CalendarController extends Controller {
	
    
    public function index()
    {  
        $validator = JsValidator::make($this->validationRules);
        $categories = CalendarCategories::whereRaw('memberId = ?',array(Auth::id()))->get();
        $results['categories']= $categories;
        $results['categories_entries']="";
        $results['categoryId']=0;
        return view('calendar.index')->with('results',$results);
    }
    
    public function calendarstore()
    {  
        CalendarCategories::store(Request::all());  
        return redirect('calendar')->with('message', 'Category Saved');
    }
    
    public function loadRecordAjax()
    {
       $calendar_entry = CalendarCategoriesEntry::select(DB::raw("id,eventName, description,DATE_FORMAT(startDate,'%Y-%m-%dT%T') as startDate, DATE_FORMAT(endDate,'%Y-%m-%dT%T') as endDate, repeatEvent, eventBy, monthBy, weekDay"))->whereRaw("id = ?",array(Request::input("id")))->get();
       $result['calendar_entry'] = $calendar_entry;
       return $result; 
    }
    
    public function calendarshow($id)
    {
        $categories = CalendarCategories::whereRaw('memberId = ?',array(Auth::id()))->get();
        $categories_entries = CalendarCategoriesEntry::select(DB::raw("id, eventName as title, DATE_FORMAT(startDate,'%Y-%m-%dT%T') as start, DATE_FORMAT(endDate,'%Y-%m-%dT%T') as end"))->whereRaw('memberId = ? and categoryId = ?',array(Auth::id(),$id))->get();
        $results['categories']= $categories;
        //$results['categories_entries']= json_encode($categories_entries);
        $results['categories_entries']= json_encode($categories_entries);
        $results['categoryId']=$id;
        return view('calendar.index')->with('results',$results);
    }
    
    public function calendarenrtystore()
    { 
        CalendarCategoriesEntry::store(Request::all());  
        if(Request::input('isAjax',0)==1){// FOR AJAX MSG
            return "Successfully updated";
        }else{
            $redirect = "calendar/show/".Request::input('categoryId');
            $msg = (Request::input("id",0)==0)?"Successfully saved":"Successfully updated";
            return redirect($redirect)->with('message', $msg);
        }
    }
}