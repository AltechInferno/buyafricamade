import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:flutter_screenutil/flutter_screenutil.dart';

class BAMOutlinedInput extends StatelessWidget {
  BAMOutlinedInput({Key key, @required this.hintText, this.maxLines = 1, this.validator, this.controller, this.trailing, this.obscureText = false, this.enableSuggestions = true, this.autocorrect = false}) : super(key: key);
  final String hintText;
  final int maxLines;
  String Function(String) validator;
  TextEditingController controller;
  final Widget trailing;
   bool obscureText;
  bool enableSuggestions;
  bool autocorrect;
  @override
  Widget build(BuildContext context) {
    return Container(
      decoration: BoxDecoration(
        borderRadius: BorderRadius.circular(15.0.r),
        color: Colors.white,
        boxShadow: <BoxShadow>[
          BoxShadow(
            color: Color(0xFF000000).withOpacity(0.05),
            offset: Offset(0.0, 4.0),
            blurRadius: 10.0,
          ),
        ],
      ),
      child: TextFormField(
        obscureText: obscureText,
        controller: controller,
        enableSuggestions: enableSuggestions,
        autocorrect: autocorrect,
        autovalidateMode: AutovalidateMode.onUserInteraction,
        maxLines: maxLines,
        validator: validator,
        decoration: InputDecoration(
          suffixIcon: trailing,
           contentPadding: EdgeInsets.symmetric(vertical: 15.h, horizontal: 12.w),
          hintText: hintText,
          hintStyle: GoogleFonts.poppins(
              color: Color(0xFFD8D8D8),
              fontWeight: FontWeight.w400,
          ),

          border: OutlineInputBorder(
              borderSide: BorderSide.none,
              borderRadius: BorderRadius.circular(15.r)),
        ),
      ),
    );
  }
}
