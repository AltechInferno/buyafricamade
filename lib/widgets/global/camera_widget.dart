import 'package:active_ecommerce_flutter/constants/colors.dart';
import 'package:active_ecommerce_flutter/utils/ui.dart';
import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
class CameraWidget extends StatelessWidget {
  const CameraWidget({Key key, @required this.text}) : super(key: key);
  final String text;
  @override
  Widget build(BuildContext context) {
    return Column(
      children: [
        ClipOval(
          child: Container(
            width: 52.0,
            height: 52.0,
            decoration: BoxDecoration(
                gradient: primaryLinearGradient),
            child: Center(
              child: Icon(
                Icons.camera_alt,
                size: 25.0,
                color: Colors.white,
              ),
            ),
          ),
        ),
        addVerticalSpace(4),
        Text(
          text,
          style: GoogleFonts.poppins(
              fontWeight: FontWeight.w400,
              height: 1.2,
              fontSize: 10),
        )
      ],
    );
  }
}
