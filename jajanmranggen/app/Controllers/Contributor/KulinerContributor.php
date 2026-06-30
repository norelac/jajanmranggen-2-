<?php
namespace App\Controllers\Contributor;
use App\Controllers\BaseController;
use App\Models\KulinerModel;
use App\Models\CategoryModel;
use App\Models\PhotoModel;

class KulinerContributor extends BaseController
{
    protected $kulinerModel;
    protected $categoryModel;
    protected $photoModel;

    public function __construct()
    {
        $this->kulinerModel  = new KulinerModel();
        $this->categoryModel = new CategoryModel();
        $this->photoModel    = new PhotoModel();
    }

    public function index()
    {
        $data['kuliner'] = $this->kulinerModel->getByContributor(session()->get('user_id'));
        return view('contributor/kuliner/index', $data);
    }

    public function create()
    {
        $data['categories'] = $this->categoryModel->findAll();
        return view('contributor/kuliner/create', $data);
    }

    public function store()
    {
        $rules = [
            'name'        => 'required|min_length[3]|max_length[200]',
            'description' => 'permit_empty',
            'address'     => 'required',
            'category_id' => 'required|integer',
            'latitude'    => 'permit_empty|decimal',
            'longitude'   => 'permit_empty|decimal',
            'photo' => 'permit_empty|uploaded[photo]|max_size[photo,2048]|is_image[photo]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $slug = $this->kulinerModel->makeSlug($this->request->getPost('name'));

        $kuliner_id = $this->kulinerModel->insert([
            'name'           => $this->request->getPost('name'),
            'slug'           => $slug,
            'description'    => $this->request->getPost('description'),
            'address'        => $this->request->getPost('address'),
            'latitude'       => $this->request->getPost('latitude') ?: null,
            'longitude'      => $this->request->getPost('longitude') ?: null,
            'category_id'    => $this->request->getPost('category_id'),
            'contributor_id' => session()->get('user_id'),
            'status'         => 'pending',
        ]);

        // Upload foto
        $photo = $this->request->getFile('photo');
        if ($photo && $photo->isValid() && !$photo->hasMoved()) {
            $this->processAndSavePhoto($photo, $kuliner_id, true);
        }

        return redirect()->to('/contributor/kuliner')->with('success', 'Kuliner berhasil ditambahkan, menunggu persetujuan admin.');
    }

    public function edit($id)
    {
        $kuliner = $this->kulinerModel->find($id);
        if (!$kuliner || $kuliner['contributor_id'] != session()->get('user_id')) {
            return redirect()->to('/contributor/kuliner')->with('error', 'Data tidak ditemukan.');
        }
        $data['kuliner']    = $kuliner;
        $data['categories'] = $this->categoryModel->findAll();
        $data['photos']     = $this->photoModel->getByKuliner($id);
        return view('contributor/kuliner/edit', $data);
    }

    public function update($id)
    {
        $kuliner = $this->kulinerModel->find($id);
        if (!$kuliner || $kuliner['contributor_id'] != session()->get('user_id')) {
            return redirect()->to('/contributor/kuliner')->with('error', 'Akses ditolak.');
        }

        $this->kulinerModel->update($id, [
            'name'        => $this->request->getPost('name'),
            'description' => $this->request->getPost('description'),
            'address'     => $this->request->getPost('address'),
            'category_id' => $this->request->getPost('category_id'),
            'latitude'    => $this->request->getPost('latitude') ?: null,
            'longitude'   => $this->request->getPost('longitude') ?: null,
            'status'      => 'pending', // Re-review setelah edit
        ]);

        $photo = $this->request->getFile('photo');
        if ($photo && $photo->isValid() && !$photo->hasMoved()) {
            $this->processAndSavePhoto($photo, $id, false);
        }

        return redirect()->to('/contributor/kuliner')->with('success', 'Data kuliner diperbarui.');
    }

    public function delete($id)
    {
        $kuliner = $this->kulinerModel->find($id);
        if (!$kuliner || $kuliner['contributor_id'] != session()->get('user_id')) {
            return redirect()->to('/contributor/kuliner')->with('error', 'Akses ditolak.');
        }
        $this->kulinerModel->delete($id);
        return redirect()->to('/contributor/kuliner')->with('success', 'Kuliner berhasil dihapus.');
    }

    //Geocoding via Nominatim
    public function geocode()
    {
        $address = $this->request->getGet('q');
        if (!$address) {
            return $this->response->setJSON(['error' => 'Alamat kosong']);
        }
        
        //cache handling (menggunakan key baru agar tidak tabrakan dengan format cache lama)
        $cacheKey = 'geocode_suggestions_' . md5($address);
        $cache    = \Config\Services::cache();

        if ($cached = $cache->get($cacheKey)) {
            return $this->response->setJSON($cached);
        }

        $client   = \Config\Services::curlrequest();
        try {
            $response = $client->get('https://nominatim.openstreetmap.org/search', [
                'query'   => [
                    'q'            => $address, 
                    'format'       => 'json', 
                    'limit'        => 5,
                    'countrycodes' => 'id' // Membatasi pencarian di Indonesia saja
                ],
                'headers' => ['User-Agent' => 'JajanMranggen/1.0 (jajanmranggen@gmail.com)'],
            ]);

            $result = json_decode($response->getBody(), true);
        } catch (\Exception $e) {
            return $this->response->setJSON(['error' => 'Gagal menghubungi server geocoding.']);
        }

        if (empty($result)) {
            return $this->response->setJSON(['error' => 'Koordinat tidak ditemukan.']);
        }

        $data = [];
        foreach ($result as $item) {
            $data[] = [
                'lat'          => $item['lat'],
                'lng'          => $item['lon'],
                'display_name' => $item['display_name']
            ];
        }

        $cache->save($cacheKey, $data, 86400); //cache 24 jam

        return $this->response->setJSON($data);
    }

    private function processAndSavePhoto($file, $kuliner_id, $isPrimary = false)
    {
        $uploadPath = ROOTPATH . 'public/uploads/kuliner/';
        $thumbPath  = ROOTPATH . 'public/uploads/thumbnails/';

        if (!is_dir($uploadPath)) mkdir($uploadPath, 0755, true);
        if (!is_dir($thumbPath))  mkdir($thumbPath, 0755, true);

        $newName = $file->getRandomName();
        $file->move($uploadPath, $newName);

        //resize to max 800px width
        \Config\Services::image()
            ->withFile($uploadPath . $newName)
            ->resize(800, 600, true, 'width')
            ->save($uploadPath . $newName);

        //thumbnail
        \Config\Services::image()
            ->withFile($uploadPath . $newName)
            ->fit(200, 200, 'center')
            ->save($thumbPath . $newName);

        $this->photoModel->insert([
            'kuliner_id' => $kuliner_id,
            'filename'   => $newName,
            'is_primary' => $isPrimary ? 1 : 0,
        ]);
    }
}
