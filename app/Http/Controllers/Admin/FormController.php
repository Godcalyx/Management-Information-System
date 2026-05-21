<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Form;
use Illuminate\Support\Facades\Storage;
use App\Models\AuditTrail;

class FormController extends Controller
{
    public function index(Request $request)
    {
        $query = Form::query();

        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('description', 'like', '%' . $request->search . '%')
                  ->orWhere('code', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }


        $forms = $query->latest()->paginate(10);

        $categories = Form::select('category')->distinct()->pluck('category');

        $stats = [
            'total'      => Form::count(),
            'categories' => $categories->count(),
        ];

        return view('admin.forms.index', compact('forms', 'categories', 'stats'));
    }

    public function create()
    {
        return view('admin.forms.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'code'        => 'required|unique:forms,code',
            'description' => 'required',
            'category'    => 'required|string',
            'file'        => 'required|file|mimes:doc,docx,xls,xlsx,ppt,pptx,pdf,jpg,jpeg,png,gif',
        ]);

        $file = $request->file('file');
        $filePath = $file->store('forms', 'public'); // stored in storage/app/public/forms

        $form = Form::create([
            'code'        => $request->code,
            'description' => $request->description,
            'category'    => $request->category,
            'file_path'   => $filePath, // relative path
            'file_size'   => $file->getSize(),
            'file_type'   => strtolower($file->getClientOriginalExtension()),
        ]);

        AuditTrail::create([
            'user_id' => auth()->id(),
            'action'  => 'Form Created',
            'details' => json_encode(['form_id' => $form->id, 'code' => $form->code]),
        ]);

        return redirect()->route('admin.forms.index')->with('success', 'Form uploaded successfully.');
    }

    public function edit(Form $form)
    {
        return view('admin.forms.edit', compact('form'));
    }

    public function update(Request $request, Form $form)
    {
        $request->validate([
            'code'        => 'required|unique:forms,code,' . $form->id,
            'description' => 'required',
            'category'    => 'required|string',
            'file'        => 'nullable|file|mimes:doc,docx,xls,xlsx,ppt,pptx,pdf,jpg,jpeg,png,gif',
        ]);

        if ($request->hasFile('file')) {
            Storage::disk('public')->delete($form->file_path);

            $file = $request->file('file');
            $filePath = $file->store('forms', 'public');

            $form->file_path = $filePath;
            $form->file_size = $file->getSize();
            $form->file_type = strtolower($file->getClientOriginalExtension());
        }

        $form->update([
            'code'        => $request->code,
            'description' => $request->description,
            'category'    => $request->category,
        ]);

        AuditTrail::create([
            'user_id' => auth()->id(),
            'action'  => 'Form Updated',
            'details' => json_encode(['form_id' => $form->id]),
        ]);

        return redirect()->route('admin.forms.index')->with('success', 'Form updated successfully.');
    }

    public function destroy(Form $form)
    {
        Storage::disk('public')->delete($form->file_path);

        AuditTrail::create([
            'user_id' => auth()->id(),
            'action'  => 'Form Deleted',
            'details' => json_encode(['form_id' => $form->id, 'code' => $form->code]),
        ]);

        $form->delete();

        return redirect()->route('admin.forms.index')->with('success', 'Form deleted successfully.');
    }

    public function preview(Form $form)
    {
        // Correct full path
        $path = storage_path('app/public/' . $form->file_path);

        if (!file_exists($path)) {
            abort(404, 'File not found.');
        }

        $ext = strtolower($form->file_type);

        // PDF & images can load directly
        if ($ext === 'pdf' || in_array($ext, ['jpg','jpeg','png','gif'])) {
            return response()->file($path);
        }

        // Office files -> Office Viewer
        if (in_array($ext, ['doc','docx','xls','xlsx','ppt','pptx'])) {
            $publicUrl = asset('storage/' . $form->file_path);
            return redirect("https://view.officeapps.live.com/op/embed.aspx?src=" . urlencode($publicUrl));
        }

        // Other files -> download instead
        return redirect()->route('admin.forms.download', $form->id);
    }

    public function download(Form $form)
    {
        $path = storage_path('app/public/' . $form->file_path);

        if (!file_exists($path)) {
            abort(404, 'File not found.');
        }

        return response()->download($path, $form->code . '.' . $form->file_type);
    }
}
