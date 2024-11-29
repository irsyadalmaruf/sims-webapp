<?php

namespace App\Controllers;

use App\Models\ProdukModel;
use App\Models\KategoriModel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class Produk extends BaseController
{
    public function index()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/login'); 
        }
    
        $produkModel = new ProdukModel();
        $kategoriModel = new KategoriModel();
        
        $limit = 10;
        $page = $this->request->getVar('page_produk') ? $this->request->getVar('page_produk') : 1;
    
        $produkData = $produkModel->getProduk($limit, $page);
        $kategoriData = $kategoriModel->findAll();
        $total_data = $produkModel->countAllResults();
    
        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'tableHtml' => view('produk/produk', [
                    'produk' => $produkData,
                    'total_data' => $total_data,
                    'pager' => $produkModel->pager
                ])
            ]);
        }
    
        $data = [
            'produk' => $produkData,
            'kategori' => $kategoriData,
            'pager' => $produkModel->pager,
            'total_data' => $total_data,
        ];
    
        return view('produk/produk', $data);
    }
    
    public function filter()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Invalid request',
            ]);
        }
    
        $request = $this->request->getJSON();
        $search = $request->search ?? ''; 
        $category = $request->category ?? 'semua'; 
        $page = $request->page ?? 1;  
        $perPage = 10; 
    
        $produkModel = new ProdukModel();
    
        $produkData = $produkModel->getFilteredProduk($search, $category, $perPage, $page);
    
        $totalFilteredData = $produkModel->getFilteredProdukCount($search, $category);
    
        $totalData = $produkModel->countAll(); 

        $totalPages = ceil($totalFilteredData / $perPage);

        $shownData = min($totalFilteredData, $perPage);
    
        $tableHtml = '';
        if (!empty($produkData)) {
            foreach ($produkData as $key => $product) {
                $tableHtml .= '<tr>';
                $tableHtml .= '<td>' . (($page - 1) * $perPage + $key + 1) . '</td>';
                $tableHtml .= '<td><img src="' . base_url('Assets/img/produk/' . $product['image']) . '" alt="Product Image" class="product-image"></td>';
                $tableHtml .= '<td>' . $product['name'] . '</td>';
                $tableHtml .= '<td>' . $product['category_name'] . '</td>';
                $tableHtml .= '<td>' . number_format($product['price_buy'], 0, ',', '.') . '</td>';
                $tableHtml .= '<td>' . number_format($product['price_sell'], 0, ',', '.') . '</td>';
                $tableHtml .= '<td>' . $product['stock'] . '</td>';
                $tableHtml .= '<td>';
                $tableHtml .= '<a href="' . base_url('produk/edit/' . $product['id']) . '" class="btn btn-edit" style="color: #007bff;"><i class="fa fa-pencil-alt"></i></a>';
                $tableHtml .= '<a href="#" class="btn btn-delete" style="color: #f23a2e;" onclick="deleteProduct(' . $product['id'] . ')"><i class="fa fa-trash-alt"></i></a>';
                $tableHtml .= '</td>';
                $tableHtml .= '</tr>';
            }
        } else {
            $tableHtml .= '<tr><td colspan="8" class="text-center">Tidak ada data produk</td></tr>';
        }
    
        // Kirimkan data dalam response
        return $this->response->setJSON([
            'status' => 'success',
            'tableHtml' => $tableHtml,
            'totalFilteredData' => $totalFilteredData,
            'totalData' => $totalData,
            'totalPages' => $totalPages,
            'shownData' => $shownData, // Menambahkan data yang ditampilkan pada halaman ini
            'currentPage' => $page,
        ]);
    }             
            
    public function create()
    {
        $kategoriModel = new KategoriModel();
        $kategoriData = $kategoriModel->findAll();

        return view('produk/create', ['kategori' => $kategoriData]);
    }

    public function store()
    {
        $produkModel = new ProdukModel();

        $name = $this->request->getPost('name');
        $category_id = $this->request->getPost('category_id');
        $price_buy = $this->request->getPost('price_buy');
        $price_sell = $this->request->getPost('price_sell');
        $stock = $this->request->getPost('stock');
        $image = $this->request->getFile('image');

        $price_buy = (float) str_replace(',', '', $price_buy);  
        $price_sell = (float) str_replace(',', '', $price_sell); 
        $stock = (int) str_replace(',', '', $stock); 

        $validationRules = [
            'name' => 'is_unique[products.name]',
            'category_id' => 'in_list[1,2]',  
            'price_buy' => 'decimal', 
            'price_sell' => 'decimal', 
            'stock' => 'integer', 
            'image' => 'uploaded[image]|max_size[image,100]|is_image[image]|mime_in[image,image/jpg,image/jpeg,image/png]', 
        ];

        $validationMessages = [
            'name' => [
                'is_unique' => 'Nama produk sudah terdaftar.',
            ],
            'category_id' => [
                'in_list' => 'Kategori produk tidak valid.',
            ],
            'price_buy' => [
                'decimal' => 'Harga beli harus berupa angka desimal.',
            ],
            'price_sell' => [
                'decimal' => 'Harga jual harus berupa angka desimal.',
            ],
            'stock' => [
                'integer' => 'Stok barang harus berupa angka.',
            ],
            'image' => [
                'uploaded' => 'Gambar harus diunggah.',
                'max_size' => 'Ukuran gambar maksimal 100KB.',
                'is_image' => 'File yang diunggah bukan gambar.',
                'mime_in' => 'Format gambar harus JPG atau PNG.',
            ],
        ];

        if (!$this->validate($validationRules, $validationMessages)) {
            $errors = $this->validator->getErrors();

            return redirect()->back()->withInput()->with('swal', [
                'icon' => 'error',
                'title' => 'Ada Kesalahan!',
                'html' => implode('<br>', $errors),  
                'timer' => 1500, 
                'showConfirmButton' => false,
            ]);
        }

        if ($image->isValid() && !$image->hasMoved()) {
            $productName = strtolower(str_replace(' ', '', $name));
            $currentDate = \CodeIgniter\I18n\Time::now('Asia/Jakarta', 'en_ID');
            $imageName = $productName . $currentDate->format('dmyHis') . '.' . $image->getExtension();

            $image->move(ROOTPATH . 'public/Assets/img/produk', $imageName);
        } else {
            return redirect()->back()->with('errors', 'Gambar tidak valid.')->withInput();
        }

        $produkModel->save([
            'name' => $name,
            'category_id' => $category_id,
            'price_buy' => $price_buy,
            'price_sell' => $price_sell,
            'stock' => $stock,
            'image' => $imageName, 
        ]);

        return redirect()->to('/produk')->with('swal', [
            'icon' => 'success',
            'title' => 'Berhasil!',
            'text' => 'Data berhasil ditambahkan.',
        ]);
    }   

    public function edit($id)
    {
        $produkModel = new ProdukModel();
        $kategoriModel = new KategoriModel();

        $product = $produkModel->find($id);

        if (!$product) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Produk tidak ditemukan');
        }

        $kategoriData = $kategoriModel->findAll();

        $data = [
            'product' => $product,
            'kategori' => $kategoriData,
            'errors' => session()->getFlashdata('errors'),
        ];

        return view('produk/edit', $data);
    }

    public function update($id)
    {
        $produkModel = new ProdukModel();
        $product = $produkModel->find($id);
    
        if (!$product) {
            return redirect()->to('/produk')->with('errors', 'Produk tidak ditemukan.');
        }
    
        $name = $this->request->getPost('name');
        $category_id = $this->request->getPost('category_id');
        $price_buy = $this->request->getPost('price_buy');
        $price_sell = $this->request->getPost('price_sell');
        $stock = $this->request->getPost('stock');
        $image = $this->request->getFile('image');
    
        $price_buy = (float) str_replace(',', '', $price_buy);
        $price_sell = (float) str_replace(',', '', $price_sell);
        $stock = (int) str_replace(',', '', $stock);
    
        $validationRulesUpdate = [
            'name' => 'is_unique[products.name,id,{id}]',
            'category_id' => 'in_list[1,2]',
            'price_buy' => 'decimal',
            'price_sell' => 'decimal',
            'stock' => 'integer',
        ];

        if ($name === $product['name']) {
            unset($validationRulesUpdate['name']);
        }
    
        if ($image && $image->isValid() && !$image->hasMoved()) {
            $validationRulesUpdate['image'] = 'uploaded[image]|max_size[image,100]|is_image[image]|mime_in[image,image/jpg,image/jpeg,image/png]';
        }
    
        $validationMessagesUpdate = [
            'name' => [
                'is_unique' => 'Nama produk sudah terdaftar.',
            ],
            'category_id' => [
                'in_list' => 'Kategori produk tidak valid.',
            ],
            'price_buy' => [
                'decimal' => 'Harga beli harus berupa angka desimal.',
            ],
            'price_sell' => [
                'decimal' => 'Harga jual harus berupa angka desimal.',
            ],
            'stock' => [
                'integer' => 'Stok barang harus berupa angka.',
            ],
            'image' => [
                'max_size' => 'Ukuran gambar maksimal 100KB.',
                'is_image' => 'File yang diunggah bukan gambar.',
                'mime_in' => 'Format gambar harus JPG atau PNG.',
            ],
        ];
    
        if (!$this->validate($validationRulesUpdate, $validationMessagesUpdate)) {
            $errors = $this->validator->getErrors();
    
            return redirect()->back()->withInput()->with('swal', [
                'icon' => 'error',
                'title' => 'Ada Kesalahan!',
                'html' => implode('<br>', $errors),
                'timer' => 1500,
                'showConfirmButton' => false,
            ]);
        }
    
        $imageName = $product['image'];
    
        if ($image && $image->isValid() && !$image->hasMoved()) {
            $productName = strtolower(str_replace(' ', '', $name));
            $currentDate = \CodeIgniter\I18n\Time::now('Asia/Jakarta', 'en_ID');
            $imageName = $productName . $currentDate->format('dmyHis') . '.' . $image->getExtension();
    
            if ($product['image'] && file_exists(ROOTPATH . 'public/Assets/img/produk/' . $product['image'])) {
                unlink(ROOTPATH . 'public/Assets/img/produk/' . $product['image']);
            }
    
            $image->move(ROOTPATH . 'public/Assets/img/produk', $imageName);
        }
    
        $produkModel->save([
            'id' => $id,
            'name' => $name,
            'category_id' => $category_id,
            'price_buy' => $price_buy,
            'price_sell' => $price_sell,
            'stock' => $stock,
            'image' => $imageName,
        ]);
    
        return redirect()->to('/produk')->with('swal', [
            'icon' => 'success',
            'title' => 'Berhasil!',
            'text' => 'Data berhasil diubah.',
        ]);
    }    

    public function delete($id)
    {
        $produkModel = new ProdukModel();
        
        $product = $produkModel->find($id);
        if ($product) {
            $imagePath = 'Assets/img/produk/' . $product['image'];
    
            if (file_exists($imagePath)) {
                unlink($imagePath); 
            } else {
                return redirect()->to('/produk')->with('swal', [
                    'icon' => 'warning',
                    'title' => 'Peringatan!',
                    'text' => 'Gambar tidak ditemukan.',
                    'timer' => 1500,
                    'showConfirmButton' => false,
                ]);
            }

            $produkModel->delete($id);
    
            return redirect()->to('/produk')->with('swal', [
                'icon' => 'success',
                'title' => 'Berhasil!',
                'text' => 'Data berhasil dihapus.',
                'timer' => 1500,  
                'showConfirmButton' => false,  
            ]);
        }
    
        return redirect()->to('/produk')->with('swal', [
            'icon' => 'error',
            'title' => 'Gagal!',
            'text' => 'Data tidak ditemukan.',
            'timer' => 1500,
            'showConfirmButton' => false,
        ]);
    }     
    
    public function exportExcel()
    {
        $produkModel = new ProdukModel();
        $search = $this->request->getGet('search') ?? '';
        $category = $this->request->getGet('category') ?? 'semua';
        $produkData = $produkModel->getFilteredProduk($search, $category);
    
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
    
        $sheet->setCellValue('A1', 'Data Produk');
        $sheet->mergeCells('A1:F1');
        $sheet->getStyle('A1')->getFont()->setSize(16)->setBold(true);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    
        $sheet->setCellValue('A2', 'No');
        $sheet->setCellValue('B2', 'Nama Barang');
        $sheet->setCellValue('C2', 'Kategori Produk');
        $sheet->setCellValue('D2', 'Harga Beli');
        $sheet->setCellValue('E2', 'Harga Jual');
        $sheet->setCellValue('F2', 'Stok');
    
        $sheet->getStyle('A2:F2')->getFont()->setBold(true);
        $sheet->getStyle('A2:F2')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
        $sheet->getStyle('A2:F2')->getFill()->getStartColor()->setRGB('F23A2E');
        $sheet->getStyle('A2:F2')->getFont()->getColor()->setRGB('FFFFFF'); 
        $sheet->getStyle('A2:F2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    
        $row = 3; 
        $nomor = 1; 
        foreach ($produkData as $product) {
            $sheet->setCellValue('A' . $row, $nomor); 
            $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->setCellValue('B' . $row, $product['name']);
            $sheet->setCellValue('C' . $row, $product['category_name']);
            $sheet->setCellValue('D' . $row, number_format($product['price_buy'], 0, ',', ','));
            $sheet->setCellValue('E' . $row, number_format($product['price_sell'], 0, ',', ','));
            $sheet->setCellValue('F' . $row, $product['stock']);
            $row++;
            $nomor++; 
        }
    
        $sheet->getColumnDimension('A')->setWidth(10); 
        $sheet->getColumnDimension('B')->setWidth(30); 
        $sheet->getColumnDimension('C')->setWidth(25); 
        $sheet->getColumnDimension('D')->setWidth(20); 
        $sheet->getColumnDimension('E')->setWidth(20); 
        $sheet->getColumnDimension('F')->setWidth(15); 
    
        date_default_timezone_set('Asia/Jakarta');
        $fileName = 'DataProduk_' . date('dmY-His') . '.xlsx';
    
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $fileName . '"');
        header('Cache-Control: max-age=0');
    
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
    }    
}