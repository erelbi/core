<?php

use App\User;
use Illuminate\Foundation\Inspiring;

Artisan::command('administrator',function (){
    // Check if Admin user is exists.
    $user = User::where('email','admin@liman.app')->first();
    if($user){
        if(!$this->confirm('Administrator kullanıcısı silinip tekrar eklenecektir. Devam etmek istiyor musunuz?')){
            return false;
        }
        $user->delete();
    }

    $password = str_random("16");
    $user = User::create([
        'name' => "administrator",
        'email' => "admin@liman.app",
        'password' => Hash::make($password),
        'status' => "1"
    ]);
    $perm = new \App\Permission();
    $perm->user_id = $user->_id;
    $perm->server = [];
    $perm->save();
    $this->comment("Administrator kullanıcısı eklendi. Parola : " . $password . "");
})->describe('Create administrator account to use');

Artisan::command('test',function (){
    if(is_dir(app_path('setup'))){
        $this->comment('var');
    }else{
        $this->comment(__('Kurulacak bir şey bulunamadı.'));
    }
});

Artisan::command('import',function(){
    // if(is_dir('/liman/import')){
    //     $files = array_values(preg_grep('~\.(lmne)$~',scandir('/liman/import')));
    //     dd($files);
    // }else{
    //     $this->comment("Yuklenecek eklenti bulunamadi.");
    //     return;
    // }
    $file = file_get_contents('/liman/duygu.json');
    $json = json_decode($file,true);

    $new = new \App\Extension();
    $new->fill($json);
    $new->save();
    $this->comment($new->_id);
    // $this->comment(app_path('setup'));
});

Artisan::command('export',function(){
    $model = \App\Extension::where('_id','5c41f9438f2fa562a716a152')->first();
    $path = resource_path('views' . DIRECTORY_SEPARATOR .'extensions' . DIRECTORY_SEPARATOR . strtolower($model->name));
    $zip = new ZipArchive();
    $zip->open('/liman/export/deneme.lmne',ZipArchive::CREATE | ZipArchive::OVERWRITE);
    $files = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($path),
        RecursiveIteratorIterator::LEAVES_ONLY
    );
    foreach ($files as $name => $file){
        // Skip directories (they would be added automatically)
        if (!$file->isDir())
        {
            // Get real and relative path for current file
            $filePath = $file->getRealPath();
            $relativePath = substr($filePath, strlen($path) + 1);

            // Add current file to archive
            $zip->addFile($filePath, 'views/' . $relativePath);
        }
    }
    # DB Extraction
    $random = '/tmp/' . str_random(6);
    file_put_contents($random,$model->toJson());
    $zip->addFile($random,'db.json');
    $zip->close();
    $this->comment('file saved!');
    
});