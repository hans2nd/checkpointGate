-keep class io.flutter.** { *; }
-keep class io.flutter.plugins.** { *; }


-keep class kotlin.** { *; }
-dontwarn kotlin.**


-keep class androidx.** { *; }
-dontwarn androidx.**


-keep class okhttp3.** { *; }
-keep class okio.** { *; }
-dontwarn okhttp3.**
-dontwarn okio.**


-keep class com.google.gson.** { *; }
-keep class * {
    @com.google.gson.annotations.SerializedName <fields>;
}


-keep class retrofit2.** { *; }
-dontwarn retrofit2.**


-keep class com.google.mlkit.** { *; }
-dontwarn com.google.mlkit.**

-keep class androidx.camera.** { *; }
-dontwarn androidx.camera.**



-keep class com.google.android.gms.** { *; }
-dontwarn com.google.android.gms.**


-keep class io.flutter.plugins.sharedpreferences.** { *; }
-keep class com.it_nomads.fluttersecurestorage.** { *; }


-keep class net.sqlcipher.** { *; }
-dontwarn net.sqlcipher.**


-keep class dev.fluttercommunity.plus.connectivity.** { *; }


-keep class android.webkit.** { *; }
-dontwarn android.webkit.**


-keepattributes *Annotation*
-keepattributes Signature
-keepattributes Exceptions


-keepclassmembers enum * {
    public static **[] values();
    public static ** valueOf(java.lang.String);
}


-keep class com.example.mobile.** { *; }


-assumenosideeffects class android.util.Log {
    public static *** d(...);
    public static *** v(...);
    public static *** i(...);
}


-dontwarn javax.annotation.**
-dontwarn org.codehaus.mojo.**

-keepclasseswithmembernames class * {
    native <methods>;
}


-keep class com.google.android.play.core.** { *; }
-dontwarn com.google.android.play.core.**