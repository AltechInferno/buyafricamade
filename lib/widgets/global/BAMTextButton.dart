import 'package:active_ecommerce_flutter/constants/colors.dart';
import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';

class BAMTextButton extends StatelessWidget {
  const BAMTextButton({Key key, @required this.text, @required this.onPress}) : super(key: key);
  final String text;
  final VoidCallback onPress;
  @override
  Widget build(BuildContext context) {
    return   TextButton(

      onPressed:onPress,
      child: Text(
        text,
        style: GoogleFonts.poppins(
          fontSize: 16.0,
          fontWeight: FontWeight.w500,
          color:primaryColor,
        ),
      ),
      style:ButtonStyle(

        padding: MaterialStateProperty.all<EdgeInsets>(EdgeInsets.zero),
        tapTargetSize: MaterialTapTargetSize.shrinkWrap,

      ),
    );

  }
}
