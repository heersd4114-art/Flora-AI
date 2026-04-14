import 'dart:io';
import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:image_picker/image_picker.dart';
import 'package:provider/provider.dart';
import '../services/api_service.dart';
import '../providers/auth_provider.dart';
import 'product_detail_screen.dart';

class ScanScreen extends StatefulWidget {
  @override
  _ScanScreenState createState() => _ScanScreenState();
}

class _ScanScreenState extends State<ScanScreen> {
  File? _image;
  bool _isLoading = false;
  Map<String, dynamic>? _result;
  final _picker = ImagePicker();

  double _confidenceValue(Map<String, dynamic> result) {
    final raw = result['confidence'];
    if (raw is num) return raw.toDouble().clamp(0.0, 1.0);
    return double.tryParse(raw?.toString() ?? '')?.clamp(0.0, 1.0) ?? 0.0;
  }

  bool _isNoPlantDetected(Map<String, dynamic> result) {
    final plant = (result['plant'] ?? '').toString().toLowerCase().trim();
    final display = (result['display_name'] ?? '').toString().toLowerCase().trim();
    final disease = (result['disease'] ?? '').toString().toLowerCase().trim();
    return plant == 'unknown object' ||
        plant == 'not a plant' ||
        plant == 'scan error' ||
        display == 'scan error' ||
        disease == 'not a plant' ||
        disease == 'invalid image';
  }

  Future<void> _pickImage(ImageSource source) async {
    try {
      final pickedFile = await _picker.pickImage(
        source: source,
        imageQuality: 50, // Prevents Android Out-of-Memory crashes
        maxWidth: 1024,
        maxHeight: 1024,
      );
      if (pickedFile != null) {
        setState(() {
          _image = File(pickedFile.path);
          _result = null;
        });
        _uploadImage();
      }
    } catch (e) {
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(SnackBar(
          content: Text('Camera Access Error: Ensure permissions are granted. ($e)'),
          backgroundColor: Colors.red[800],
        ));
      }
    }
  }

  Future<void> _uploadImage() async {
    if (_image == null) return;
    setState(() => _isLoading = true);

    final auth = Provider.of<AuthProvider>(context, listen: false);

    try {
      final response = await ApiService.postMultipart(
        'identify.php',
        {'user_id': auth.user?.id.toString() ?? ''},
        _image!.path,
        'image'
      );

      if (response['status'] == 'success') {
        setState(() => _result = response);
      } else {
        ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text(response['message'] ?? 'Failed to analyze image')));
      }
    } catch (e) {
      ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text('Error: $e')));
    } finally {
      if (mounted) setState(() => _isLoading = false);
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Color(0xFFF3F6F8),
      appBar: AppBar(
        elevation: 0,
        backgroundColor: Colors.transparent,
        flexibleSpace: Container(
          decoration: BoxDecoration(
            gradient: LinearGradient(
              colors: [Color(0xFF2E7D32), Color(0xFF1B5E20)],
              begin: Alignment.topLeft,
              end: Alignment.bottomRight,
            ),
          ),
        ),
        title: Row(
          children: [
            Icon(Icons.center_focus_strong, color: Colors.white),
            SizedBox(width: 10),
            Text('Flora ID Scanner', style: GoogleFonts.poppins(fontWeight: FontWeight.w600, color: Colors.white)),
          ],
        ),
      ),
      body: SingleChildScrollView(
        physics: BouncingScrollPhysics(),
        child: Column(
          children: [
            Container(
              margin: EdgeInsets.all(16),
              height: 320,
              width: double.infinity,
              decoration: BoxDecoration(
                color: Colors.white,
                borderRadius: BorderRadius.circular(24),
                boxShadow: [
                  BoxShadow(color: Colors.black.withOpacity(0.05), blurRadius: 20, offset: Offset(0, 10))
                ]
              ),
              child: ClipRRect(
                borderRadius: BorderRadius.circular(24),
                child: _image == null
                    ? Column(
                        mainAxisAlignment: MainAxisAlignment.center,
                        children: [
                          Container(
                            padding: EdgeInsets.all(20),
                            decoration: BoxDecoration(
                              color: Colors.green[50],
                              shape: BoxShape.circle,
                            ),
                            child: Icon(Icons.document_scanner_rounded, size: 48, color: Colors.green[700]),
                          ),
                          SizedBox(height: 16),
                          Text('Ready to Analyze', style: GoogleFonts.poppins(fontSize: 18, fontWeight: FontWeight.w600, color: Colors.green[900])),
                          SizedBox(height: 8),
                          Text('Upload a leaf or plant photo', style: GoogleFonts.poppins(color: Colors.grey[600]))
                        ],
                      )
                    : Image.file(_image!, fit: BoxFit.cover),
              )
            ),
            
            Padding(
              padding: EdgeInsets.symmetric(horizontal: 16),
              child: Row(
                children: [
                   Expanded(
                     child: ElevatedButton.icon(
                        onPressed: () => _pickImage(ImageSource.camera),
                        icon: Icon(Icons.camera_alt_rounded),
                        label: Text('Camera'),
                        style: ElevatedButton.styleFrom(
                          padding: EdgeInsets.symmetric(vertical: 16),
                          backgroundColor: Color(0xFF2E7D32),
                          foregroundColor: Colors.white,
                          shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
                          elevation: 0
                        ),
                      ),
                   ),
                   SizedBox(width: 12),
                   Expanded(
                     child: ElevatedButton.icon(
                        onPressed: () => _pickImage(ImageSource.gallery),
                        icon: Icon(Icons.photo_library_rounded),
                        label: Text('Gallery'),
                        style: ElevatedButton.styleFrom(
                          padding: EdgeInsets.symmetric(vertical: 16),
                          backgroundColor: Colors.white,
                          foregroundColor: Color(0xFF2E7D32),
                          shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
                          elevation: 0,
                          side: BorderSide(color: Color(0xFF2E7D32).withOpacity(0.2))
                        ),
                      ),
                   ),
                ],
              ),
            ),
            
            if (_isLoading) 
              Padding(
                padding: EdgeInsets.all(40), 
                child: Column(
                  children: [
                    CircularProgressIndicator(valueColor: AlwaysStoppedAnimation<Color>(Color(0xFF2E7D32))),
                  ]
                )
              ),
              
            if (_result != null && !_isLoading) ...[
              Padding(
                padding: EdgeInsets.all(16),
                child: Container(
                  decoration: BoxDecoration(
                    color: Colors.white,
                    borderRadius: BorderRadius.circular(24),
                    boxShadow: [
                      BoxShadow(color: Colors.black.withOpacity(0.04), blurRadius: 24, offset: Offset(0, 12))
                    ],
                    border: Border.all(color: Colors.grey.withOpacity(0.1))
                  ),
                  child: Padding(
                    padding: EdgeInsets.all(24),
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Row(
                          children: [
                            Container(
                               padding: EdgeInsets.all(12),
                               decoration: BoxDecoration(
                                 color: _isNoPlantDetected(_result!) ? Colors.red[50] : Colors.green[50],
                                 borderRadius: BorderRadius.circular(16)
                               ),
                               child: Icon(
                                 _isNoPlantDetected(_result!) ? Icons.error_outline : Icons.eco_rounded, 
                                 color: _isNoPlantDetected(_result!) ? Colors.red[700] : Colors.green[700],
                                 size: 32
                               )
                            ),
                            SizedBox(width: 16),
                            Expanded(
                              child: Column(
                                crossAxisAlignment: CrossAxisAlignment.start,
                                children: [
                                  Text(
                                    _result!['display_name'] ?? _result!['plant'] ?? 'Unknown',
                                    style: GoogleFonts.poppins(fontSize: 22, fontWeight: FontWeight.bold, color: Colors.blueGrey[900]),
                                  ),
                                  SizedBox(height: 4),
                                  if (!_isNoPlantDetected(_result!))
                                    Container(
                                      padding: EdgeInsets.symmetric(horizontal: 10, vertical: 4),
                                      decoration: BoxDecoration(
                                        color: Colors.green[50],
                                        borderRadius: BorderRadius.circular(20),
                                        border: Border.all(color: Colors.green.withOpacity(0.2))
                                      ),
                                      child: Text(
                                        'Confidence: ${(_confidenceValue(_result!) * 100).toStringAsFixed(1)}%',
                                        style: GoogleFonts.poppins(color: Colors.green[700], fontSize: 12, fontWeight: FontWeight.w600),
                                      ),
                                    ),
                                ],
                              ),
                            ),
                          ],
                        ),
                        
                        Divider(height: 40, color: Colors.grey[200]),
                        
                        Text('Health Status', style: GoogleFonts.poppins(fontSize: 14, color: Colors.grey[500], fontWeight: FontWeight.w600)),
                        SizedBox(height: 4),
                        Text(
                          _result!['disease'] ?? 'Healthy', 
                          style: GoogleFonts.poppins(
                            fontSize: 18, 
                            fontWeight: FontWeight.w600,
                            color: _isNoPlantDetected(_result!) ? Colors.red[700] : (_result!['disease'] == 'Healthy' ? Colors.green[600] : Colors.orange[800])
                          )
                        ),
                        
                        SizedBox(height: 24),
                        
                        if (!_isNoPlantDetected(_result!)) ...[
                            if ((_result!['ai_analysis'] ?? '').toString().trim().isNotEmpty) ...[
                              Container(
                                padding: EdgeInsets.all(16),
                                decoration: BoxDecoration(
                                  color: Colors.green[50],
                                  borderRadius: BorderRadius.circular(16)
                                ),
                                child: Column(
                                  crossAxisAlignment: CrossAxisAlignment.start,
                                  children: [
                                    Row(
                                      children: [
                                        Icon(Icons.auto_awesome, size: 16, color: Colors.green[800]),
                                        SizedBox(width: 8),
                                        Text('Flora AI Analysis', style: GoogleFonts.poppins(fontWeight: FontWeight.bold, color: Colors.green[800])),
                                      ]
                                    ),
                                    SizedBox(height: 8),
                                    Text((_result!['ai_analysis'] ?? '').toString(), style: GoogleFonts.poppins(color: Colors.green[900], height: 1.5)),
                                  ]
                                )
                              ),
                              SizedBox(height: 24),
                            ],
                            
                            Text('Care Instructions', style: GoogleFonts.poppins(fontSize: 14, color: Colors.grey[500], fontWeight: FontWeight.w600)),
                            SizedBox(height: 8),
                            Text(
                              ((_result!['care_tips_raw'] ?? '').toString().trim().isNotEmpty
                                      ? _result!['care_tips_raw']
                                      : _result!['care_tips'])
                                  ??
                                  'No special care needed.',
                              style: GoogleFonts.poppins(color: Colors.grey[800], height: 1.5),
                            ),
                            
                            if (_result!['treatment_steps'] is List && (_result!['treatment_steps'] as List).isNotEmpty) ...[
                              SizedBox(height: 24),
                              Text('Treatment Steps', style: GoogleFonts.poppins(fontSize: 14, color: Colors.grey[500], fontWeight: FontWeight.w600)),
                              SizedBox(height: 12),
                              ...(_result!['treatment_steps'] as List)
                                  .map((step) => Padding(
                                    padding: EdgeInsets.only(bottom: 12),
                                    child: Row(
                                      crossAxisAlignment: CrossAxisAlignment.start,
                                      children: [
                                        Icon(Icons.check_circle, color: Colors.green[400], size: 20),
                                        SizedBox(width: 12),
                                        Expanded(child: Text(step.toString(), style: GoogleFonts.poppins(color: Colors.grey[800], height: 1.4))),
                                      ],
                                    ),
                                  ))
                                  .toList(),
                            ],
                            
                            if (_result!['cures'] != null && _result!['cures'].isNotEmpty) ...[
                              Divider(height: 40, color: Colors.grey[200]),
                              Text('Recommended Treatments', style: GoogleFonts.poppins(fontWeight: FontWeight.bold, fontSize: 16, color: Colors.blueGrey[900])),
                              SizedBox(height: 16),
                              Container(
                                height: 160,
                                child: ListView.builder(
                                  scrollDirection: Axis.horizontal,
                                  physics: BouncingScrollPhysics(),
                                  itemCount: _result!['cures'].length,
                                  itemBuilder: (context, idx) {
                                    final cure = _result!['cures'][idx];
                                    return GestureDetector(
                                      onTap: () {
                                        Navigator.push(context, MaterialPageRoute(builder: (_) => ProductDetailScreen(product: cure)));
                                      },
                                      child: Container(
                                        width: 140,
                                        margin: EdgeInsets.only(right: 16),
                                        decoration: BoxDecoration(
                                          color: Colors.white,
                                          borderRadius: BorderRadius.circular(16),
                                          border: Border.all(color: Colors.grey.withOpacity(0.15)),
                                          boxShadow: [
                                            BoxShadow(color: Colors.black.withOpacity(0.02), blurRadius: 10, offset: Offset(0, 4))
                                          ]
                                        ),
                                        child: Column(
                                          crossAxisAlignment: CrossAxisAlignment.start,
                                          children: [
                                            ClipRRect(
                                              borderRadius: BorderRadius.vertical(top: Radius.circular(15)),
                                              child: Image.network(
                                                ApiService.baseUrl.replaceAll('/api', '') + '/' + (cure['image_url'] ?? ''),
                                                height: 90, width: double.infinity, fit: BoxFit.cover,
                                                errorBuilder: (c, e, s) => Container(height: 90, color: Colors.grey[100], child: Icon(Icons.image_not_supported, color: Colors.grey[400]))
                                              )
                                            ),
                                            Padding(
                                              padding: EdgeInsets.all(10),
                                              child: Column(
                                                crossAxisAlignment: CrossAxisAlignment.start,
                                                children: [
                                                  Text(cure['name'] ?? '', maxLines: 1, overflow: TextOverflow.ellipsis, style: GoogleFonts.poppins(fontSize: 13, fontWeight: FontWeight.w600, color: Colors.blueGrey[900])),
                                                  SizedBox(height: 4),
                                                  Text('₹${cure['price']}', style: GoogleFonts.poppins(fontSize: 13, color: Colors.green[700], fontWeight: FontWeight.bold)),
                                                ]
                                              )
                                            )
                                          ]
                                        )
                                      )
                                    );
                                  }
                                )
                              )
                            ]
                        ] else ...[
                             Container(
                                padding: EdgeInsets.all(16),
                                decoration: BoxDecoration(
                                  color: Colors.red[50],
                                  borderRadius: BorderRadius.circular(16),
                                  border: Border.all(color: Colors.red.withOpacity(0.2))
                                ),
                                child: Row(
                                  children: [
                                    Icon(Icons.info_outline, color: Colors.red[700]),
                                    SizedBox(width: 12),
                                    Expanded(child: Text("We could not detect a valid plant in this image. No care tips or treatments are available. Please capture a clear image of a leaf or stem and try again.", style: GoogleFonts.poppins(color: Colors.red[900], height: 1.4))),
                                  ]
                                )
                              )
                        ]
                      ],
                    ),
                  ),
                ),
              ),
              SizedBox(height: 40)
            ]
          ],
        ),
      ),
    );
  }
}
