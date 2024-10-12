<?php
namespace App\Http\Controllers\Frontend;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Todo;
use PDF;
use Log;



class PDFController extends Controller
{
    
    public function viewPDF(Request $request)
    {
        $todoId = $request->id;

        $todo = Todo::find($todoId);
        $research_result = $todo->research_result = $todo->research_result ?? 'No research done yet';
        \Log::info($todo->item);
        \Log::info($research_result);
        $pdf = PDF::loadView('pdf.research', ["content"=>$research_result, "title"=>$todo->item])
        ->setPaper('a4', 'portrait');

        return $pdf->stream();

    }


    public function downloadPDF(Request $request)
    {
        $todoId = $request->id;

        $todo = Todo::find($todoId);
        $research_result = $todo->research_result = $todo->research_result ?? 'No research done yet';
        $pdf = PDF::loadView('pdf.research', ["content"=>$research_result, "title"=>$todo->item])->setPaper('a4', 'portrait');
        return $pdf->download($todo->item.'.pdf');
    }
}
